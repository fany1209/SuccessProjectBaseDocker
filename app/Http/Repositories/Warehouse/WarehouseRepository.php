<?php

namespace App\Http\Repositories\Warehouse;

use App\Models\Cli;
use App\Models\Concept;
use App\Models\Control;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\ProductionMaterialRequest;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WarehouseRepository
{
    protected Warehouse $warehouse;

    public function __construct(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     * Obtiene el listado completo de almacenes.
     */
    public function all(): Collection
    {
        return $this->warehouse->all();
    }

    /**
     * Busca un almacén por su ID.
     */
    public function find(int $id): ?Warehouse
    {
        return $this->warehouse->find($id);
    }

    /**
     * Obtiene todos los conceptos.
     */
    public function getAllConcepts(): Collection
    {
        return Concept::all();
    }

    /**
     * Obtiene todas las ubicaciones.
     */
    public function getAllLocations(): Collection
    {
        return Location::all();
    }

    /**
     * Obtiene el catálogo de productos con inventario activo de manera única.
     */
    public function getProductsInventory(): Collection
    {
        return Inventory::select('products.product_id', 'products.name', 'products.unit')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->distinct()
            ->get();
    }

    /**
     * Resumen de stock agrupado por producto y almacén en ubicaciones CLI.
     */
    public function getInventorySummary(): Collection
    {
        return DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('products.name', 'products.unit', DB::raw('SUM(cli.net_weight) as net_weight'), 'locations.warehouse_id')
            ->groupBy('products.name', 'locations.warehouse_id', 'products.unit')
            ->get();
    }

    /**
     * Ubicaciones que actualmente cuentan con existencias.
     */
    public function getAvailableLocations(): Collection
    {
        return DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('locations.name')
            ->get();
    }

    /**
     * Detalle de lotes de inventario calculando el remanente en almacén (stock_wh).
     */
    public function getInventoryStockWH(): Collection
    {
        return DB::table('inventory')
            ->leftJoin('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->leftJoin('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'inventory.inventory_id',
                'products.product_id',
                'inventory.batch',
                'products.name',
                'inventory.stock',
                DB::raw('CAST(inventory.stock - COALESCE(SUM(cli.net_weight), 0) AS DECIMAL(10,3)) as stock_wh')
            )
            ->groupBy(
                'inventory.inventory_id',
                'products.product_id',
                'inventory.batch',
                'products.name',
                'inventory.stock'
            )
            ->get();
    }

    /**
     * Evalúa el estatus de registro para las horas de control de temperatura del día actual.
     *
     * @param int|null $warehouseId
     * @param array<int, string> $hours
     * @param string $timezone
     * @return array<int, bool>
     */
    public function getFeedback(?int $warehouseId, array $hours, string $timezone): array
    {
        $feedback = array_fill(0, count($hours), false);

        if ($warehouseId) {
            $controls = Control::where('warehouse_id', $warehouseId)
                ->whereDate('created_at', Carbon::today($timezone))
                ->get();

            foreach ($hours as $index => $hour) {
                $hourInt = (int) explode(':', $hour)[0];

                $feedback[$index] = $controls->contains(function ($c) use ($hourInt, $timezone) {
                    $controlDate = Carbon::parse($c->created_at)->timezone($timezone);
                    return (int) $controlDate->format('H') === $hourInt;
                });
            }
        }

        return $feedback;
    }

    /**
     * Almacena mediciones de temperatura y humedad dentro de una transacción ACID.
     *
     * @param int $warehouseId
     * @param array<int, array<string, mixed>> $measurements
     * @param array<string, mixed> $meta
     * @return Collection
     */
    public function storeTemperatureMeasurements(int $warehouseId, array $measurements, array $meta): Collection
    {
        return DB::transaction(function () use ($warehouseId, $measurements, $meta) {
            $created = collect();

            foreach ($measurements as $measurement) {
                $control = Control::create([
                    'temperature'  => $measurement['temperature'],
                    'humidity'     => $measurement['humidity'],
                    'warehouse_id' => $warehouseId,
                    'user_id'      => $meta['user_id'] ?? null,
                    'host_ip'      => $meta['host_ip'] ?? null,
                    'host_user'    => $meta['host_user'] ?? null,
                    'host_name'    => $meta['host_name'] ?? null,
                ]);

                $created->push($control);
            }

            return $created;
        });
    }

    /**
     * Consulta el detalle de productos y peso ubicados en una locación específica.
     */
    public function getInfoLocation(string $location): Collection
    {
        return DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'products.name as pName',
                'concepts.name as cName',
                'bag_number',
                'quantity',
                'weight_per_unit as wpu',
                'products.unit',
                'net_weight as total',
                'batch'
            )
            ->where('locations.name', $location)
            ->get();
    }

    /**
     * Consulta dinámica de existencias con filtros de búsqueda y concepto.
     */
    public function getWarehouseStock(?string $concept = null, ?string $search = null): Collection
    {
        $query = DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'cli_id',
                'locations.name as lName',
                'products.name as pName',
                'concepts.name as cName',
                'bag_number',
                'quantity',
                DB::raw('concat(format(weight_per_unit,2)," ",products.unit) as weight_per_unit'),
                DB::raw('concat(format(net_weight,2)," ",products.unit) as net_weight'),
                'batch',
                'products.unit'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', '%' . $search . '%')
                    ->orWhere('locations.name', 'like', '%' . $search . '%')
                    ->orWhere('concepts.name', 'like', '%' . $search . '%')
                    ->orWhere('inventory.batch', 'like', '%' . $search . '%');
            });
        }

        if (!empty($concept)) {
            $query->where('concepts.concept_id', $concept);
        }

        return $query->get();
    }

    /**
     * Obtiene el listado de solicitudes de material para producción con sus partidas.
     */
    public function getProductionRequests(): Collection
    {
        return ProductionMaterialRequest::with('items')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Marca una solicitud de material como 'Surtido' aplicando bloqueo pesimista contra TOCTOU.
     */
    public function attendProductionRequest(int $id): ProductionMaterialRequest
    {
        return DB::transaction(function () use ($id) {
            $matReq = ProductionMaterialRequest::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $matReq->status = 'Surtido';
            $matReq->save();

            return $matReq;
        });
    }

    /**
     * Obtiene las partidas de una solicitud de material vinculando el product_id si existe coincidencia.
     */
    public function getRequestItems(int $id): Collection
    {
        $request = ProductionMaterialRequest::with('items')->findOrFail($id);

        return $request->items->map(function ($item) {
            $product = DB::table('products')->where('name', $item->product_name)->first();
            return [
                'product_id'   => $product ? $product->product_id : null,
                'product_name' => $item->product_name,
                'quantity'     => $item->quantity,
            ];
        });
    }

    /**
     * Obtiene el resumen detallado de BigBags agrupado por producto y totales consolidados.
     *
     * @param int|null $warehouseId
     * @return array<string, mixed>
     */
    public function getBigBagsSummary(?int $warehouseId = null): array
    {
        $query = DB::table('cli')
            ->join('inventory', 'cli.inventory_id', '=', 'inventory.inventory_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->leftJoin('locations', 'locations.location_id', '=', 'cli.location_id')
            ->leftJoin('warehouses', 'warehouses.warehouse_id', '=', 'locations.warehouse_id')
            ->where(function ($q) {
                $q->whereNotNull('cli.bag_number')
                  ->orWhere('products.name', 'like', '%BGBG%')
                  ->orWhere('products.unit', 'BigBags');
            })
            ->where('cli.quantity', '>', 0);

        if ($warehouseId) {
            $query->where('warehouses.warehouse_id', $warehouseId);
        }

        $raw = $query->select(
            'cli.cli_id',
            'cli.location_id',
            'cli.bag_number',
            'cli.protein',
            'cli.packing_list',
            'cli.weight_per_unit',
            'cli.net_weight',
            'cli.quantity',
            'cli.created_at',
            'inventory.batch',
            'products.product_id',
            'products.name as product_name',
            'products.unit',
            'locations.name as location_name',
            'warehouses.warehouse_id',
            'warehouses.name as warehouse_name'
        )
        ->orderBy('products.name')
        ->orderBy('cli.bag_number')
        ->get();

        $products = $raw->groupBy('product_id')->map(function ($items) {
            $first = $items->first();
            $totalWeight = $items->sum(function ($i) {
                return (float) ($i->net_weight ?: $i->weight_per_unit);
            });
            $count = $items->count();

            return [
                'product_id'   => $first->product_id,
                'product_name' => $first->product_name,
                'unit'         => $first->unit,
                'total_bags'   => $count,
                'total_weight' => round($totalWeight, 3),
                'avg_weight'   => $count > 0 ? round($totalWeight / $count, 3) : 0,
                'bags'         => $items->map(function ($b) {
                    return [
                        'cli_id'         => $b->cli_id,
                        'location_id'    => $b->location_id,
                        'bag_number'     => $b->bag_number,
                        'protein'        => $b->protein,
                        'packing_list'   => $b->packing_list,
                        'weight'         => round((float) ($b->net_weight ?: $b->weight_per_unit), 3),
                        'batch'          => $b->batch,
                        'warehouse_id'   => $b->warehouse_id,
                        'warehouse_name' => $b->warehouse_name ?? 'Sin asignar',
                        'location_name'  => $b->location_name ?? 'Sin asignar',
                        'date'           => $b->created_at ? date('d-m-Y H:i', strtotime($b->created_at)) : null,
                    ];
                })->values(),
            ];
        })->values();

        $grandTotalWeight = $raw->sum(function ($i) {
            return (float) ($i->net_weight ?: $i->weight_per_unit);
        });

        return [
            'grand_total_bags'   => $raw->count(),
            'grand_total_weight' => round($grandTotalWeight, 3),
            'products_count'     => $products->count(),
            'products'           => $products,
        ];
    }

    /**
     * Actualiza la información de un BigBag individual aplicando bloqueo pesimista y ACID.
     *
     * @param int $cliId
     * @param array<string, mixed> $data
     * @return Cli|null
     */
    public function updateBigBag(int $cliId, array $data): ?Cli
    {
        return DB::transaction(function () use ($cliId, $data) {
            $cli = Cli::lockForUpdate()->find($cliId);
            if (!$cli) {
                return null;
            }

            $weight = (float) $data['weight'];
            $qty = (float) ($cli->quantity > 0 ? $cli->quantity : 1);

            $updatePayload = [
                'weight_per_unit' => $weight,
                'net_weight'      => $qty * $weight,
            ];

            if (array_key_exists('bag_number', $data)) {
                $updatePayload['bag_number'] = $data['bag_number'];
            }
            if (array_key_exists('protein', $data)) {
                $updatePayload['protein'] = $data['protein'];
            }
            if (array_key_exists('packing_list', $data)) {
                $updatePayload['packing_list'] = $data['packing_list'];
            }
            if (!empty($data['location_id'])) {
                $updatePayload['location_id'] = (int) $data['location_id'];
            }

            $cli->update($updatePayload);

            return $cli->fresh();
        });
    }
}
