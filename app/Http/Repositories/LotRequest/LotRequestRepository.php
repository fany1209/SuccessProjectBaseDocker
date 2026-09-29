<?php

namespace App\Http\Repositories\LotRequest;

use App\Models\LotRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LotRequestRepository
{
    /**
     * @var LotRequest
     */
    protected LotRequest $model;

    /**
     * LotRequestRepository constructor.
     *
     * @param LotRequest $model
     */
    public function __construct(LotRequest $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado completo de peticiones de lote.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        return $query->latest('id')->get();
    }

    /**
     * Busca una petición de lote por su ID.
     *
     * @param int|string $id
     * @return LotRequest|null
     */
    public function find($id): ?LotRequest
    {
        return $this->model->find($id);
    }

    /**
     * Conteo de peticiones de lote pendientes.
     *
     * @return int
     */
    public function countPending(): int
    {
        return $this->model->where('status', 'pendiente')->count();
    }

    /**
     * Conteo de peticiones de lote terminadas.
     *
     * @return int
     */
    public function countCompleted(): int
    {
        return $this->model->where('status', 'terminado')->count();
    }

    /**
     * Listado de peticiones de lote pendientes ordenadas por fecha.
     *
     * @return Collection
     */
    public function getPending(): Collection
    {
        return $this->model->where('status', 'pendiente')
            ->orderByDesc('requested_at')
            ->get();
    }

    /**
     * Obtiene el conjunto de datos para el componente DataTables.
     *
     * @return array<string, mixed>
     */
    public function getDatatableData(): array
    {
        $rows = DB::table('lot_requests')
            ->select([
                'id',
                'department',
                DB::raw("DATE_FORMAT(requested_at, '%Y-%m-%d %H:%i') as requested_at"),
                'status',
                'comments',
                'product',
                'quantity',
                'provider',
                'collector',
                'sector',
                'sku',
                'batch'
            ])
            ->orderByDesc('requested_at')
            ->orderByDesc('id')
            ->limit(500)
            ->get();

        $productSkus = DB::table('lot_requests')
            ->whereNotNull('sku')
            ->where('sku', '!=', '')
            ->whereNotNull('product')
            ->where('product', '!=', '')
            ->select('product', 'sku')
            ->distinct()
            ->get()
            ->groupBy('product')
            ->map(function ($items) {
                return $items->pluck('sku')->toArray();
            });

        $productBatches = DB::table('lot_requests')
            ->whereNotNull('batch')
            ->where('batch', '!=', '')
            ->whereNotNull('product')
            ->where('product', '!=', '')
            ->select('product', 'batch')
            ->distinct()
            ->get()
            ->groupBy('product')
            ->map(function ($items) {
                return $items->pluck('batch')->toArray();
            });

        return [
            'lots'           => $rows,
            'productSkus'    => $productSkus,
            'productBatches' => $productBatches,
        ];
    }

    /**
     * Registra una nueva petición de lote bajo una transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return LotRequest
     */
    public function create(array $data): LotRequest
    {
        return DB::transaction(function () use ($data) {
            $data['requested_at'] = $data['requested_at'] ?? now();
            $data['status']       = $data['status'] ?? 'pendiente';

            return $this->model->create($data);
        });
    }

    /**
     * Actualiza el estatus y metadatos de la petición con bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    public function updateStatus($id, array $data): ?array
    {
        return DB::transaction(function () use ($id, $data) {
            $lot = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$lot) {
                return null;
            }

            $nuevoEstado = ($data['status'] ?? '') === 'terminado'
                ? 'terminado'
                : 'pendiente';

            $dataToUpdate = ['status' => $nuevoEstado];

            if (array_key_exists('sku', $data) && $data['sku'] !== null) {
                $dataToUpdate['sku'] = $data['sku'];
            }
            if (array_key_exists('batch', $data) && $data['batch'] !== null) {
                $dataToUpdate['batch'] = $data['batch'];
            }

            $lot->update($dataToUpdate);

            $pending = $this->countPending();

            return [
                'lot'     => $lot->fresh(),
                'pending' => $pending,
            ];
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
