<?php

namespace App\Http\Repositories\TransportLine;

use App\Models\TransportLine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TransportLineRepository
{
    /**
     * @var TransportLine
     */
    protected TransportLine $model;

    /**
     * TransportLineRepository constructor.
     *
     * @param TransportLine $model
     */
    public function __construct(TransportLine $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado de líneas de transporte con soporte para filtros.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Busca una línea de transporte por su ID.
     *
     * @param int|string $id
     * @return TransportLine|null
     */
    public function find($id): ?TransportLine
    {
        return $this->model->where('transport_line_id', $id)->first();
    }

    /**
     * Registra una nueva línea de transporte en transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return TransportLine
     */
    public function create(array $data): TransportLine
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    /**
     * Actualiza una línea de transporte con bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return TransportLine|null
     */
    public function update($id, array $data): ?TransportLine
    {
        return DB::transaction(function () use ($id, $data) {
            $line = $this->model->newQuery()
                ->where('transport_line_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$line) {
                return null;
            }

            $line->update($data);
            return $line->fresh();
        });
    }

    /**
     * Elimina una línea de transporte con bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $line = $this->model->newQuery()
                ->where('transport_line_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$line) {
                return false;
            }

            return (bool) $line->delete();
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
