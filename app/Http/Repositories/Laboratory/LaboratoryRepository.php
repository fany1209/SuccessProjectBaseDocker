<?php

namespace App\Http\Repositories\Laboratory;

use App\Models\CustomerSampleRequest;
use App\Models\LaboratorySample;
use App\Models\Product;
use App\Models\SalidaMuestra;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\NewSampleRequestNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class LaboratoryRepository
{
    protected CustomerSampleRequest $customerSampleRequest;
    protected LaboratorySample $laboratorySample;

    public function __construct(CustomerSampleRequest $customerSampleRequest, LaboratorySample $laboratorySample)
    {
        $this->customerSampleRequest = $customerSampleRequest;
        $this->laboratorySample = $laboratorySample;
    }

    public function getIndexData(): array
    {
        $products = Product::orderBy('name')->get(['product_id', 'name', 'sku']);

        $batchesByProduct = DB::table('inventory')
            ->select('product_id', 'batch')
            ->whereNotNull('batch')
            ->whereRaw("TRIM(batch) <> ''")
            ->distinct()
            ->orderBy('product_id')
            ->orderBy('batch')
            ->get()
            ->groupBy('product_id')
            ->map(fn($g) => $g->pluck('batch')->values());

        $suppliers = DB::table('suppliers')
            ->select('supplier_id', 'supplier_code', 'name')
            ->orderBy('name')
            ->get();

        $customers = DB::table('customers')
            ->select(
                'customer_id',
                'name',
                'phone',
                'email',
                'address',
                'district',
                'city',
                'state',
                'postal_code',
                'country'
            )
            ->orderBy('name')
            ->get()
            ->map(function ($c) {
                $parts = array_filter([
                    $c->address,
                    $c->district,
                    $c->city,
                    $c->state,
                    $c->postal_code ? ('C.P. ' . $c->postal_code) : null,
                    $c->country,
                ]);
                $c->full_address = implode(', ', $parts);
                return $c;
            });

        $samples = DB::table('laboratory_samples as ls')
            ->leftJoin('products as p', function ($join) {
                $join->on(
                    DB::raw("CONVERT(p.sku USING utf8mb4)"),
                    '=',
                    DB::raw("CONVERT(ls.sku USING utf8mb4)")
                );
            })
            ->leftJoin('suppliers as sup', function ($join) {
                $join->on(
                    DB::raw("CONVERT(sup.name USING utf8mb4)"),
                    '=',
                    DB::raw("CONVERT(ls.proveedor USING utf8mb4)")
                );
            })
            ->select([
                'ls.*',
                'p.product_id',
                'sup.supplier_id',
            ])
            ->orderBy('ls.id', 'desc')
            ->limit(200)
            ->get();

        $folios = DB::table('soil_internal_analyses')
            ->whereNotNull('report_code')
            ->whereRaw("TRIM(report_code) <> ''")
            ->orderByDesc('id')
            ->limit(200)
            ->pluck('report_code')
            ->unique()
            ->values();

        $customerRequests = $this->customerSampleRequest->orderByDesc('id')->limit(200)->get();

        return compact(
            'products',
            'batchesByProduct',
            'suppliers',
            'customers',
            'samples',
            'folios',
            'customerRequests'
        );
    }

    public function getCustomerRequests(): Collection
    {
        return $this->customerSampleRequest->with('items.product')->orderBy('id', 'desc')->limit(500)->get();
    }

    public function findCustomerRequest(int $id): ?CustomerSampleRequest
    {
        return $this->customerSampleRequest->with(['items.product'])->find($id);
    }

    public function findCustomerRequestOrFail(int $id): CustomerSampleRequest
    {
        return $this->customerSampleRequest->with(['items.product'])->findOrFail($id);
    }

    public function storeCustomerRequest(array $data, array $items): CustomerSampleRequest
    {
        return DB::transaction(function () use ($data, $items) {
            $folioFinal = $data['folio'] ?? null;

            if (empty($folioFinal)) {
                $lastRecord = $this->customerSampleRequest->lockForUpdate()->orderBy('folio', 'desc')->first();

                if ($lastRecord && preg_match('/SCR-(\d+)/', $lastRecord->folio, $matches)) {
                    $nextNumber = (int) $matches[1] + 1;
                } else {
                    $nextNumber = 1;
                }

                $folioFinal = 'SCR-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
            }

            $data['folio'] = $folioFinal;
            $data['status'] = 0;

            foreach (['entrega_paqueteria', 'entrega_personal_empresa', 'entrega_recoleccion_planta', 'entrega_otro'] as $field) {
                $data[$field] = !empty($data[$field]) ? 1 : 0;
            }

            if (empty($data['entrega_otro'])) {
                $data['entrega_otro_txt'] = null;
            }

            $record = $this->customerSampleRequest->create($data);

            foreach ($items as $item) {
                $itemData = $item;
                $itemCheckboxes = [
                    'pres_ziploc', 'pres_whirlpak', 'pres_metalizada', 'pres_frasco', 'pres_bidon', 'pres_otro',
                    'docs_cc', 'docs_ft', 'docs_hs', 'docs_otro'
                ];
                foreach ($itemCheckboxes as $field) {
                    $itemData[$field] = isset($item[$field]) && filter_var($item[$field], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                }
                if (empty($itemData['pres_otro'])) {
                    $itemData['pres_otro_txt'] = null;
                }
                if (empty($itemData['docs_otro'])) {
                    $itemData['docs_otro_txt'] = null;
                }

                $record->items()->create($itemData);
            }

            try {
                $usersToNotify = User::role(['Admin', 'Laboratory'])->get();
                if ($usersToNotify->isNotEmpty()) {
                    Notification::send($usersToNotify, new NewSampleRequestNotification($record));
                }
            } catch (\Throwable $e) {
            }

            return $record->load('items.product');
        });
    }

    public function updateCustomerRequest(int $id, array $data, array $items): CustomerSampleRequest
    {
        return DB::transaction(function () use ($id, $data, $items) {
            $record = $this->customerSampleRequest->where('id', $id)->lockForUpdate()->firstOrFail();

            foreach (['entrega_paqueteria', 'entrega_personal_empresa', 'entrega_recoleccion_planta', 'entrega_otro'] as $field) {
                $data[$field] = !empty($data[$field]) ? 1 : 0;
            }

            if (empty($data['entrega_otro'])) {
                $data['entrega_otro_txt'] = null;
            }

            $record->update($data);
            $record->items()->delete();

            foreach ($items as $item) {
                $itemData = $item;
                $itemCheckboxes = [
                    'pres_ziploc', 'pres_whirlpak', 'pres_metalizada', 'pres_frasco', 'pres_bidon', 'pres_otro',
                    'docs_cc', 'docs_ft', 'docs_hs', 'docs_otro'
                ];
                foreach ($itemCheckboxes as $field) {
                    $itemData[$field] = isset($item[$field]) && filter_var($item[$field], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                }
                if (empty($itemData['pres_otro'])) {
                    $itemData['pres_otro_txt'] = null;
                }
                if (empty($itemData['docs_otro'])) {
                    $itemData['docs_otro_txt'] = null;
                }

                $record->items()->create($itemData);
            }

            return $record->load('items.product');
        });
    }

    public function updateCustomerRequestStatus(int $id, int $status): bool
    {
        return DB::transaction(function () use ($id, $status) {
            $record = $this->customerSampleRequest->where('id', $id)->lockForUpdate()->firstOrFail();
            $record->status = $status;
            return $record->save();
        });
    }

    public function deleteCustomerRequest(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->customerSampleRequest->where('id', $id)->lockForUpdate()->first();
            if (!$record) {
                return false;
            }
            $record->items()->delete();
            return (bool) $record->delete();
        });
    }

    public function storeSampleInv(array $data, ?string $folioInput = null): LaboratorySample
    {
        return DB::transaction(function () use ($data, $folioInput) {
            if (!empty($data['proveedor'])) {
                $supplierName = trim($data['proveedor']);
                $existsSupplier = Supplier::where('name', $supplierName)->first();
                if (!$existsSupplier) {
                    $next = Supplier::max('supplier_id') + 1;
                    Supplier::create([
                        'name'          => $supplierName,
                        'supplier_code' => 'SP' . $next,
                    ]);
                }
            }

            if (!empty($data['producto'])) {
                $productName = trim($data['producto']);
                $existsProduct = Product::where('name', $productName)->first();
                $sku = $data['sku'] ?? null;

                if (!$existsProduct) {
                    if (empty($sku)) {
                        $lastProduct = Product::orderBy('product_id', 'desc')->first();
                        $nextId = $lastProduct ? $lastProduct->product_id + 1 : 1;
                        $sku = 'LAB' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
                        $data['sku'] = $sku;
                    }

                    Product::create([
                        'name'        => $productName,
                        'sku'         => $sku,
                        'sat_code'    => '01010101',
                        'category_id' => 1,
                        'is_public'   => 1,
                    ]);
                } else {
                    if (empty($sku)) {
                        $data['sku'] = $existsProduct->sku;
                    }
                }
            }

            if (!empty($folioInput)) {
                $folioFinal = $folioInput;
            } else {
                $maxFolio = DB::table('laboratory_samples')
                    ->lockForUpdate()
                    ->selectRaw('MAX(CAST(folio AS UNSIGNED)) AS max_folio')
                    ->value('max_folio');

                $nextNum = max((int) $maxFolio, 1479) + 1;
                $folioFinal = (string) $nextNum;
            }

            $data['folio'] = $folioFinal;
            if (empty($data['status'])) {
                $data['status'] = 'Fuera de laboratorio';
            }

            return $this->laboratorySample->create($data);
        });
    }

    public function getMuestras(?string $motivo = null, ?string $search = null): Collection
    {
        $query = DB::table('salida_muestras')
            ->leftJoin('products', 'salida_muestras.product_id', '=', 'products.product_id')
            ->select('salida_muestras.*', 'products.name as nombre_producto_original');

        if (!empty($motivo)) {
            $query->where('salida_muestras.motivo_salida', $motivo);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('salida_muestras.folio_muestra', 'LIKE', "%$search%")
                    ->orWhere('salida_muestras.sku', 'LIKE', "%$search%")
                    ->orWhere('products.name', 'LIKE', "%$search%")
                    ->orWhere('salida_muestras.nombre_comercial', 'LIKE', "%$search%");
            });
        }

        return $query->orderBy('salida_muestras.created_at', 'desc')->get();
    }

    public function findMuestra(int $id): ?object
    {
        return DB::table('salida_muestras')->where('id', $id)->first();
    }

    public function updateMuestra(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $salida = DB::table('salida_muestras')->where('id', $id)->lockForUpdate()->first();
            if (!$salida) {
                return false;
            }

            $data['updated_at'] = now();
            DB::table('salida_muestras')->where('id', $id)->update($data);
            return true;
        });
    }

    public function deleteMuestra(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $salida = DB::table('salida_muestras')->where('id', $id)->lockForUpdate()->first();
            if (!$salida) {
                return false;
            }

            DB::table('salida_muestras')->where('id', $id)->delete();
            return true;
        });
    }

    public function processSampleOutput(array $validated, string $productName, string $sku, string $fechaSalida, float $salidaG): void
    {
        DB::transaction(function () use ($validated, $fechaSalida, $salidaG, $productName, $sku) {
            $q = DB::table('laboratory_samples')->lockForUpdate();

            if (!empty($validated['folio_muestra'])) {
                $q->where('folio', $validated['folio_muestra']);
            } elseif (!empty($sku)) {
                $q->where('sku', $sku);
            } else {
                $q->where('producto', $productName);
            }

            $row = $q->where('stock_final', '>', 0)->orderByDesc('id')->first();

            if (!$row) {
                throw new \InvalidArgumentException("No hay stock disponible para el producto: $productName");
            }

            $stockActual = (float) $row->stock_final;
            $loQueYaHabiaSalido = (float) ($row->cantidad_salida ?? 0);

            if ($salidaG > $stockActual) {
                throw new \InvalidArgumentException("La salida solicitada ($salidaG) es mayor al stock disponible ($stockActual).");
            }

            $nuevaSalidaTotal = round($loQueYaHabiaSalido + $salidaG, 2);
            $newStatus = ($nuevaSalidaTotal >= (float) $row->stock_inicial || ($stockActual - $salidaG) <= 0)
                ? 'Fuera de laboratorio'
                : ($row->status ?? 'Fuera de laboratorio');

            DB::table('laboratory_samples')->where('id', $row->id)->update([
                'cantidad_salida' => $nuevaSalidaTotal,
                'fecha_salida'    => $fechaSalida,
                'motivo_salida'   => $validated['motivo_salida'] ?? $row->motivo_salida,
                'cliente'         => $validated['dest_nombre'] ?? $row->cliente,
                'status'          => $newStatus,
                'updated_at'      => now(),
            ]);

            DB::table('salida_muestras')->insert([
                'folio_muestra'              => $validated['folio_muestra'],
                'product_id'                 => $validated['product_id'],
                'fecha_salida'               => $fechaSalida,
                'nombre_comercial'           => $validated['nombre_comercial'] ?? $productName,
                'sku'                        => $sku,
                'lote'                       => $validated['lote'] ?? null,
                'um'                         => $validated['um'] ?? null,
                'cantidad'                   => $validated['cantidad'] ?? null,
                'descripcion'                => $validated['descripcion'] ?? null,
                'motivo_salida'              => $validated['motivo_salida'] ?? null,
                'motivo_otro'                => $validated['motivo_otro'] ?? null,
                'entrega_paqueteria'         => !empty($validated['entrega_paqueteria']) ? 1 : 0,
                'entrega_recoleccion_planta' => !empty($validated['entrega_recoleccion_planta']) ? 1 : 0,
                'entrega_personal_empresa'   => !empty($validated['entrega_personal_empresa']) ? 1 : 0,
                'entrega_otro'               => !empty($validated['entrega_otro']) ? 1 : 0,
                'entrega_otro_txt'           => $validated['entrega_otro_txt'] ?? null,
                'paq_empresa'                => $validated['paq_empresa'] ?? null,
                'paq_guia'                   => $validated['paq_guia'] ?? null,
                'dest_nombre'                => $validated['dest_nombre'] ?? null,
                'dest_direccion'             => $validated['dest_direccion'] ?? null,
                'dest_recibe'                => $validated['dest_recibe'] ?? null,
                'dest_correo'                => $validated['dest_correo'] ?? null,
                'dest_telefono'              => $validated['dest_telefono'] ?? null,
                'docs_cc'                    => !empty($validated['docs_cc']) ? 1 : 0,
                'docs_ft'                    => !empty($validated['docs_ft']) ? 1 : 0,
                'docs_hs'                    => !empty($validated['docs_hs']) ? 1 : 0,
                'docs_otro'                  => !empty($validated['docs_otro']) ? 1 : 0,
                'docs_otro_txt'              => $validated['docs_otro_txt'] ?? null,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ]);
        });
    }

    public function getSamplesForExport(): Collection
    {
        return DB::table('laboratory_samples')
            ->select([
                'id',
                'folio',
                'tipo_muestra',
                'proveedor',
                'sku',
                'producto',
                'stock_inicial',
                'presentacion',
                'ubicacion_stock',
                'fecha_entrada',
                'fecha_salida',
                'cantidad_salida',
                'motivo_salida',
                'solicitante',
                'recolector',
                'cliente',
                'status',
                'stock_final',
                'created_at',
            ])
            ->orderBy('id', 'desc')
            ->get();
    }
}
