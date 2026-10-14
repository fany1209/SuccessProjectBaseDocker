<?php

namespace App\Http\Repositories\ProductInf;

use App\Models\Cli;
use App\Models\ProductQr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductInfRepository
{
    protected ProductQr $model;
    protected Cli $cliModel;

    public function __construct(ProductQr $model, Cli $cliModel)
    {
        $this->model = $model;
        $this->cliModel = $cliModel;
    }

    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = is_dir(base_path('../public_html')) ? base_path('../public_html') : base_path('public');
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    public function getAll(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['product', 'location.warehouse', 'concept', 'inventory', 'cli', 'creator'])
            ->orderBy('id', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('uuid', 'like', "%{$search}%")
                  ->orWhere('batch', 'like', "%{$search}%")
                  ->orWhere('bag_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('location', function ($lq) use ($search) {
                      $lq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?ProductQr
    {
        return $this->model->newQuery()
            ->with(['product', 'location.warehouse', 'concept', 'inventory', 'cli.location.warehouse', 'creator'])
            ->where('uuid', $uuid)
            ->first();
    }

    public function getLocationProducts(string $locationName): Collection
    {
        $items = DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'cli.cli_id',
                'cli.uuid',
                'cli.detailed_description',
                'inventory.inventory_id',
                'products.product_id',
                'locations.location_id',
                'concepts.concept_id',
                'products.name as pName',
                'concepts.name as cName',
                'locations.name as lName',
                'cli.bag_number',
                'cli.quantity',
                'cli.weight_per_unit as wpu',
                'products.unit',
                'cli.net_weight as total',
                'inventory.batch'
            )
            ->where('locations.name', $locationName)
            ->get();

        // Enlazar estado de QR existente si ya fue generado
        return $items->map(function ($item) {
            $existingQr = $this->model->newQuery()
                ->where('cli_id', $item->cli_id)
                ->orWhere(function ($q) use ($item) {
                    if (!empty($item->uuid)) {
                        $q->where('uuid', $item->uuid);
                    }
                })
                ->first();

            $item->has_qr = !is_null($existingQr);
            $item->qr_uuid = $existingQr ? $existingQr->uuid : $item->uuid;
            $item->existing_description = $existingQr ? $existingQr->description : $item->detailed_description;

            return $item;
        });
    }

    public function getWarehouseMapData(): array
    {
        $warehouses = DB::table('warehouses')
            ->select('warehouse_id', 'name')
            ->get();

        $availableLocations = DB::table('cli')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->select('locations.name')
            ->distinct()
            ->get();

        return [
            'warehouses'          => $warehouses,
            'available_locations' => $availableLocations,
        ];
    }

    public function createOrUpdateQr(array $data, int $userId): ProductQr
    {
        return DB::transaction(function () use ($data, $userId) {
            $cliId = !empty($data['cli_id']) ? (int) $data['cli_id'] : null;
            $cli = null;
            $uuid = null;

            if ($cliId) {
                // Bloqueo pesimista para evitar colisiones concurrentes (Anti-TOCTOU)
                $cli = $this->cliModel->newQuery()
                    ->lockForUpdate()
                    ->with(['inventory.product', 'location', 'concept'])
                    ->find($cliId);

                if ($cli) {
                    if (empty($cli->uuid)) {
                        $cli->uuid = (string) Str::uuid();
                    }
                    $cli->detailed_description = $data['description'];
                    $cli->save();

                    $uuid = $cli->uuid;
                }
            }

            if (!$uuid) {
                $uuid = !empty($data['uuid']) ? (string) $data['uuid'] : (string) Str::uuid();
            }

            // Buscar si ya existe un registro de QR asociado
            $qrRecord = null;
            if ($cliId) {
                $qrRecord = $this->model->newQuery()
                    ->lockForUpdate()
                    ->where('cli_id', $cliId)
                    ->first();
            }

            if (!$qrRecord) {
                $qrRecord = $this->model->newQuery()
                    ->lockForUpdate()
                    ->where('uuid', $uuid)
                    ->first();
            }

            // Datos base del snapshot
            $productId = $cli && $cli->inventory ? $cli->inventory->product_id : ($data['product_id'] ?? null);
            $inventoryId = $cli ? $cli->inventory_id : ($data['inventory_id'] ?? null);
            $locationId = $cli ? $cli->location_id : ($data['location_id'] ?? null);
            $conceptId = $cli ? $cli->concept_id : ($data['concept_id'] ?? null);
            $bagNumber = $cli ? $cli->bag_number : ($data['bag_number'] ?? null);
            $batch = $cli && $cli->inventory ? $cli->inventory->batch : ($data['batch'] ?? null);
            $quantity = $cli ? $cli->quantity : ($data['quantity'] ?? null);
            $wpu = $cli ? $cli->weight_per_unit : ($data['weight_per_unit'] ?? null);
            $netWeight = $cli ? $cli->net_weight : ($data['net_weight'] ?? null);
            $unit = $cli && $cli->inventory && $cli->inventory->product ? $cli->inventory->product->unit : ($data['unit'] ?? 'kg');

            $payload = [
                'uuid'            => $uuid,
                'cli_id'          => $cliId,
                'inventory_id'    => $inventoryId,
                'product_id'      => $productId,
                'location_id'     => $locationId,
                'concept_id'      => $conceptId,
                'bag_number'      => $bagNumber,
                'batch'           => $batch,
                'quantity'        => $quantity,
                'weight_per_unit' => $wpu,
                'net_weight'      => $netWeight,
                'unit'            => $unit,
                'description'     => $data['description'],
                'created_by'      => $userId,
            ];

            if ($qrRecord) {
                $qrRecord->update($payload);
            } else {
                $qrRecord = $this->model->create($payload);
            }

            return $qrRecord->load(['product', 'location.warehouse', 'concept', 'inventory', 'cli', 'creator']);
        });
    }

    public function deleteByUuid(string $uuid): bool
    {
        return DB::transaction(function () use ($uuid) {
            $qrRecord = $this->model->newQuery()
                ->lockForUpdate()
                ->where('uuid', $uuid)
                ->first();

            if (!$qrRecord) {
                return false;
            }

            // Desvincular de cli si existía asociación
            if ($qrRecord->cli_id) {
                $cli = $this->cliModel->newQuery()
                    ->lockForUpdate()
                    ->find($qrRecord->cli_id);

                if ($cli) {
                    $cli->uuid = null;
                    $cli->detailed_description = null;
                    $cli->save();
                }
            } else {
                $cliByUuid = $this->cliModel->newQuery()
                    ->lockForUpdate()
                    ->where('uuid', $uuid)
                    ->first();

                if ($cliByUuid) {
                    $cliByUuid->uuid = null;
                    $cliByUuid->detailed_description = null;
                    $cliByUuid->save();
                }
            }

            return (bool) $qrRecord->delete();
        });
    }
}
