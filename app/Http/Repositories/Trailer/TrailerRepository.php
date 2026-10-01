<?php

namespace App\Http\Repositories\Trailer;

use App\Models\Trailer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TrailerRepository
{
    /**
     * @var Trailer
     */
    protected Trailer $model;

    /**
     * TrailerRepository constructor.
     *
     * @param Trailer $model
     */
    public function __construct(Trailer $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado de remolques con unión a líneas de transporte y soporte para filtros.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery()
            ->select('trailers.*', 'transport_lines.name as name')
            ->join('transport_lines', 'transport_lines.transport_line_id', '=', 'trailers.transport_line_id');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('transport_lines.name', 'like', "%{$search}%")
                  ->orWhere('trailers.plate', 'like', "%{$search}%")
                  ->orWhere('trailers.type', 'like', "%{$search}%")
                  ->orWhere('trailers.unit_number', 'like', "%{$search}%")
                  ->orWhere('trailers.color', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['t_line'])) {
            $query->where('trailers.transport_line_id', $filters['t_line']);
        }

        return $query->orderBy('trailers.trailer_id', 'desc')->get();
    }

    /**
     * Busca un remolque por su ID junto con su línea de transporte.
     *
     * @param int|string $id
     * @return Trailer|null
     */
    public function find($id): ?Trailer
    {
        return $this->model->newQuery()
            ->with('transportLine')
            ->where('trailer_id', $id)
            ->first();
    }

    /**
     * Registra un nuevo remolque en transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return Trailer
     */
    public function create(array $data): Trailer
    {
        return DB::transaction(function () use ($data) {
            $trailer = $this->model->create($data);
            $trailer->load('transportLine');
            return $trailer;
        });
    }

    /**
     * Actualiza un remolque con bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return Trailer|null
     */
    public function update($id, array $data): ?Trailer
    {
        return DB::transaction(function () use ($id, $data) {
            $trailer = $this->model->newQuery()
                ->where('trailer_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$trailer) {
                return null;
            }

            $trailer->update($data);
            $trailer->load('transportLine');
            return $trailer->fresh(['transportLine']);
        });
    }

    /**
     * Elimina un remolque con bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $trailer = $this->model->newQuery()
                ->where('trailer_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$trailer) {
                return false;
            }

            return (bool) $trailer->delete();
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
