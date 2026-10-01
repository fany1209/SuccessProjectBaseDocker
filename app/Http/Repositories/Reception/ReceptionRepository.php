<?php

namespace App\Http\Repositories\Reception;

use App\Models\Product;
use App\Models\ReceptionOfSample;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReceptionRepository
{
    protected ReceptionOfSample $model;

    public function __construct(ReceptionOfSample $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado de recepciones para el DataTables.
     *
     * @return Collection
     */
    public function datatable(): Collection
    {
        $rows = DB::table('reception_of_samples as r')
            ->leftJoin('products as p', 'p.product_id', '=', 'r.product_id')
            ->orderByDesc('r.fecha_entrada')
            ->limit(500)
            ->select(
                'r.id',
                'r.batch',
                'r.fecha_entrada',
                DB::raw('COALESCE(p.name, r.nombre_comercial) as product'),
                DB::raw('COALESCE(r.estatus, 0) as estatus')
            )
            ->get();

        return $rows->map(function ($r) {
            return [
                'id'       => $r->id,
                'product'  => $r->product ?? '—',
                'batch'    => $r->batch ?? '—',
                'entry_at' => $r->fecha_entrada ? Carbon::parse($r->fecha_entrada)->format('Y-m-d') : '',
                'estatus'  => (int) $r->estatus,
                'status'   => (int) $r->estatus === 1 ? 'TERMINADO' : 'PENDIENTE',
            ];
        });
    }

    /**
     * Busca una recepción de muestra por ID.
     */
    public function find(int $id): ?ReceptionOfSample
    {
        return $this->model->with(['product', 'supplier'])->find($id);
    }

    /**
     * Busca una recepción o lanza ModelNotFoundException.
     */
    public function findOrFail(int $id): ReceptionOfSample
    {
        return $this->model->with(['product', 'supplier'])->findOrFail($id);
    }

    /**
     * Obtiene lotes únicos de inventario para un producto.
     *
     * @param int|null $productId
     * @return array<int, string>
     */
    public function getBatchesForProduct(?int $productId): array
    {
        if (!$productId) {
            return [];
        }

        return DB::table('inventory')
            ->where('product_id', $productId)
            ->whereNotNull('batch')
            ->whereRaw("TRIM(batch) <> ''")
            ->distinct()
            ->pluck('batch')
            ->toArray();
    }

    /**
     * Estructura los datos completos para la visualización/edición modal.
     */
    public function getShowData(int $id): array
    {
        $rec = $this->findOrFail($id);

        $prod = $rec->product;
        $supplierId = $rec->supplier_id ?? ($rec->product_id ? $this->resolveSupplierIdForProduct((int) $rec->product_id) : null);
        $supplierName = $rec->supplier ? $rec->supplier->name : ($supplierId ? DB::table('suppliers')->where('supplier_id', $supplierId)->value('name') : null);
        $batches = $this->getBatchesForProduct($rec->product_id);

        return [
            'id'                        => $rec->id,
            'folio_muestra'             => $rec->folio_muestra,
            'product_id'                => $rec->product_id,
            'product_name'              => $prod->name ?? '',
            'sku'                       => $rec->sku ?? ($prod->sku ?? ''),
            'batch'                     => $rec->batch,
            'batches'                   => $batches,
            'nombre_comercial'          => $rec->nombre_comercial,
            'fecha_entrada'             => $rec->fecha_entrada ? Carbon::parse($rec->fecha_entrada)->format('Y-m-d') : '',
            'fecha_caducidad'           => $rec->fecha_caducidad ? Carbon::parse($rec->fecha_caducidad)->format('Y-m-d') : '',
            'descripcion'               => $rec->descripcion,
            'supplier_id'               => $supplierId,
            'supplier_name'             => $supplierName,
            'origen_muestra'            => $rec->origen_muestra,
            'origen_otro'               => $rec->origen_otro,
            'objetivo_muestra'          => $rec->objetivo_muestra ?? '',
            'objetivo_otro'             => $rec->objetivo_otro,
            'cantidad'                  => $rec->cantidad,
            'um'                        => $rec->um,
            'um_otro'                   => $rec->um_otro,
            'docs_ccf'                  => (bool) $rec->docs_ccf,
            'docs_ft'                   => (bool) $rec->docs_ft,
            'docs_hs'                   => (bool) $rec->docs_hs,
            'docs_otro'                 => (bool) $rec->docs_otro,
            'docs_otro_txt'             => $rec->docs_otro_txt,
            'observaciones_laboratorio' => $rec->observaciones_laboratorio,
            'firma_entrega_nombre'      => $rec->firma_entrega_nombre,
            'firma_recepcion_nombre'    => $rec->firma_recepcion_nombre,
            'estatus'                   => (int) ($rec->estatus ?? 0),
        ];
    }

    /**
     * Registra una nueva recepción de muestra resolviendo productos o proveedores dinámicamente.
     */
    public function store(array $data, ?string $rawProduct = null, ?string $rawSupplier = null): ReceptionOfSample
    {
        return DB::transaction(function () use ($data, $rawProduct, $rawSupplier) {
            $objArr = $data['objetivo_muestra'] ?? [];
            $objStr = is_array($objArr) ? implode(', ', $objArr) : (string) $objArr;

            if (($data['origen_muestra'] ?? null) !== 'otro') {
                $data['origen_otro'] = null;
            }
            if (is_array($objArr) && !in_array('otro', $objArr)) {
                $data['objetivo_otro'] = null;
            }
            if (($data['um'] ?? null) !== 'otro') {
                $data['um_otro'] = null;
            }

            $data['folio_muestra'] = $data['folio_muestra'] ?? ('RM-' . now()->format('Ymd-His'));

            $productId = $data['product_id'] ?? null;
            if (!$productId && !empty($rawProduct)) {
                $productName = trim($rawProduct);
                $product = Product::where('name', $productName)->first();
                if (!$product) {
                    $lastProduct = Product::orderBy('product_id', 'desc')->first();
                    $nextId = $lastProduct ? $lastProduct->product_id + 1 : 1;
                    $sku = 'LAB' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);

                    $product = Product::create([
                        'name'        => $productName,
                        'sku'         => $data['sku'] ?? $sku,
                        'sat_code'    => '01010101',
                        'category_id' => 1,
                        'is_public'   => 1,
                    ]);
                }
                $productId = $product->product_id;
                $data['sku'] = $data['sku'] ?? $product->sku;
            }

            if (!$productId) {
                throw new \InvalidArgumentException('El producto es requerido.');
            }

            $data['product_id'] = $productId;

            $supplierId = $data['supplier_id'] ?? null;
            if (!$supplierId && !empty($rawSupplier)) {
                $supplierName = trim($rawSupplier);
                $supplierId = Supplier::where('name', $supplierName)->value('supplier_id');
                if (!$supplierId) {
                    $s = Supplier::create([
                        'name'          => $supplierName,
                        'supplier_code' => 'SP' . str_pad((string) ((int) Supplier::max('supplier_id') + 1), 5, '0', STR_PAD_LEFT),
                    ]);
                    $supplierId = $s->supplier_id;
                }
            }

            $data['supplier_id'] = $supplierId;
            $data['objetivo_muestra'] = $objStr;

            foreach (['docs_ccf', 'docs_ft', 'docs_hs', 'docs_otro'] as $cb) {
                $data[$cb] = (int) (!empty($data[$cb]));
            }

            if (empty($data['docs_otro'])) {
                $data['docs_otro_txt'] = null;
            }

            return $this->model->create($data);
        });
    }

    /**
     * Actualiza una recepción aplicando bloqueo pesimista en transacción ACID.
     */
    public function update(int $id, array $data): ReceptionOfSample
    {
        return DB::transaction(function () use ($id, $data) {
            $rec = $this->model->where('id', $id)->lockForUpdate()->firstOrFail();

            $objArr = $data['objetivo_muestra'] ?? [];
            $objStr = is_array($objArr) ? implode(', ', $objArr) : (string) $objArr;

            if (($data['origen_muestra'] ?? null) !== 'otro') {
                $data['origen_otro'] = null;
            }
            if (is_array($objArr) && !in_array('otro', $objArr)) {
                $data['objetivo_otro'] = null;
            }
            if (($data['um'] ?? null) !== 'otro') {
                $data['um_otro'] = null;
            }

            foreach (['docs_ccf', 'docs_ft', 'docs_hs', 'docs_otro'] as $cb) {
                $data[$cb] = (int) (!empty($data[$cb]));
            }

            if (empty($data['docs_otro'])) {
                $data['docs_otro_txt'] = null;
            }

            $data['objetivo_muestra'] = $objStr;

            $rec->update($data);

            return $rec;
        });
    }

    /**
     * Actualiza el estatus de calidad de la recepción a 1 (TERMINADO).
     */
    public function updateQuality(int $id, array $data): ReceptionOfSample
    {
        return DB::transaction(function () use ($id, $data) {
            $rec = $this->model->where('id', $id)->lockForUpdate()->firstOrFail();

            $objArr = $data['objetivo_muestra'] ?? [];
            $objStr = is_array($objArr) ? implode(', ', $objArr) : (string) $objArr;

            foreach (['docs_ccf', 'docs_ft', 'docs_hs', 'docs_otro'] as $cb) {
                $data[$cb] = (int) (!empty($data[$cb]));
            }

            $data['objetivo_muestra'] = $objStr;
            $data['estatus'] = 1;

            $rec->update($data);

            return $rec;
        });
    }

    /**
     * Obtiene los datos preparados para la plantilla PDF de recepción de muestra (Formato 01).
     */
    public function getPdfData(int $id): array
    {
        $rec = $this->findOrFail($id);
        $productName = DB::table('products')->where('product_id', $rec->product_id)->value('name');
        $supplierName = $rec->supplier_id ? Supplier::where('supplier_id', $rec->supplier_id)->value('name') : '';

        $data = $rec->toArray();
        $data['producto']  = $productName ?? '';
        $data['proveedor'] = $supplierName ?? '';

        return [
            'data'          => $data,
            'folio_muestra' => $rec->folio_muestra,
        ];
    }

    /**
     * Elimina el registro aplicando bloqueo pesimista en transacción ACID.
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $rec = $this->model->where('id', $id)->lockForUpdate()->first();
            if (!$rec) {
                return false;
            }

            return (bool) $rec->delete();
        });
    }

    /**
     * Obtiene las recepciones con calidad pendiente (estatus 0).
     */
    public function getPendingQuality(): Collection
    {
        return $this->model->where('estatus', 0)->get();
    }

    /**
     * Resuelve el ID del proveedor a través del inventario del producto si está disponible.
     */
    protected function resolveSupplierIdForProduct(int $productId): ?int
    {
        if (Schema::hasTable('inventory')) {
            $sid = DB::table('inventory')
                ->where('product_id', $productId)
                ->whereNotNull('supplier_id')
                ->value('supplier_id');

            if ($sid) {
                return (int) $sid;
            }
        }

        return null;
    }
}
