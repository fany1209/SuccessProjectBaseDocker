<?php

namespace App\Http\Repositories\LabSample;

use App\Models\LaboratorySample;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LabSampleRepository
{
    /**
     * @var LaboratorySample
     */
    protected LaboratorySample $model;

    /**
     * LabSampleRepository constructor.
     *
     * @param LaboratorySample $model
     */
    public function __construct(LaboratorySample $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene todas las muestras de laboratorio.
     *
     * @param array<string, mixed> $filters
     * @return EloquentCollection
     */
    public function all(array $filters = []): EloquentCollection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('producto', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('proveedor', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('id')->get();
    }

    /**
     * Busca una muestra de laboratorio por su ID.
     *
     * @param int|string $id
     * @return LaboratorySample|null
     */
    public function find($id): ?LaboratorySample
    {
        return $this->model->find($id);
    }

    /**
     * Obtiene los registros formateados para DataTables con relaciones calculadas.
     *
     * @return Collection
     */
    public function getDatatableRows(): Collection
    {
        return DB::table('laboratory_samples as ls')
            ->leftJoin(
                'reception_of_samples as ros',
                'ros.folio_muestra',
                '=',
                'ls.folio'
            )
            ->leftJoin(
                'suppliers as s',
                's.supplier_id',
                '=',
                'ros.supplier_id'
            )
            ->select([
                'ls.id',
                'ls.folio',
                'ls.tipo_muestra',
                'ls.producto',
                'ls.sku',
                'ls.ubicacion_stock',
                'ls.stock_inicial',
                'ls.cantidad_salida',
                'ls.stock_final',
                'ls.fecha_entrada',
                'ls.fecha_salida',
                'ros.batch as lote',
                'ls.proveedor',
                'ls.status',
            ])
            ->orderByDesc('ls.fecha_entrada')
            ->orderByDesc('ls.id')
            ->get()
            ->map(function ($r) {
                $dash = fn($v) => (is_null($v) || $v === '') ? '—' : $v;
                $d    = fn($v) => $v
                    ? (is_string($v) ? $v : $v->format('Y-m-d'))
                    : '—';
                $num  = fn($v, $dec = 2) => is_null($v)
                    ? '—'
                    : number_format((float)$v, $dec, '.', '');

                return [
                    'id'             => $r->id,
                    'folio'          => $dash($r->folio),
                    'tipo_muestra'   => $dash($r->tipo_muestra),
                    'producto'       => $dash($r->producto),
                    'sku'            => $dash($r->sku),
                    'proveedor'      => $dash($r->proveedor),
                    'lote'           => $dash($r->lote),
                    'ubicacion'      => $dash($r->ubicacion_stock),
                    'stock_inicial'  => $num($r->stock_inicial),
                    'salida'         => $num($r->cantidad_salida),
                    'stock_final'    => $num($r->stock_final),
                    'fecha_entrada'  => $d($r->fecha_entrada),
                    'fecha_salida'   => $d($r->fecha_salida),
                    'status'         => $r->status ?? 'Fuera de laboratorio',
                    'acciones'       => '
                        <div class="flex gap-2">
                            <button class="px-2 py-1 rounded bg-emerald-600 text-white text-xs"
                                data-id="'.$r->id.'" data-action="ver">Ver</button>

                            <button class="px-2 py-1 rounded bg-amber-600 text-white text-xs"
                                data-id="'.$r->id.'" data-action="editar">Editar</button>

                            <button class="px-2 py-1 rounded bg-rose-600 text-white text-xs"
                                data-id="'.$r->id.'" data-action="eliminar">Eliminar</button>
                        </div>',
                ];
            });
    }

    /**
     * Registra una nueva muestra de laboratorio en transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return LaboratorySample
     */
    public function create(array $data): LaboratorySample
    {
        return DB::transaction(function () use ($data) {
            unset($data['stock_final']);
            return $this->model->create($data);
        });
    }

    /**
     * Actualiza una muestra de laboratorio usando bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return LaboratorySample|null
     */
    public function update($id, array $data): ?LaboratorySample
    {
        return DB::transaction(function () use ($id, $data) {
            $sample = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$sample) {
                return null;
            }

            unset($data['stock_final']);
            unset($data['folio']);

            $sample->update($data);
            return $sample->fresh();
        });
    }

    /**
     * Elimina una muestra de laboratorio aplicando bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $sample = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$sample) {
                return false;
            }

            return (bool) $sample->delete();
        });
    }

    /**
     * Resuelve rutas físicas a public_html fuera del directorio base del framework.
     *
     * @param string $subpath
     * @return string
     */
    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }
}
