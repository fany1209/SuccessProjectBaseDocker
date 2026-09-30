<?php

namespace App\Http\Repositories\Material;

use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MaterialRepository
{
    /**
     * @var Material
     */
    protected Material $model;

    /**
     * MaterialRepository constructor.
     *
     * @param Material $model
     */
    public function __construct(Material $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado de materiales ordenados por ID descendente.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('um', 'like', "%{$search}%");
            });
        }

        return $query->latest('id')->get();
    }

    /**
     * Busca un material por su ID.
     *
     * @param int|string $id
     * @return Material|null
     */
    public function find($id): ?Material
    {
        return $this->model->find($id);
    }

    /**
     * Registra un nuevo material de laboratorio calculando su stock inicial en transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return Material
     */
    public function create(array $data): Material
    {
        return DB::transaction(function () use ($data) {
            $entries = (float) ($data['entries'] ?? 0.0);
            $exits   = 0.0;
            $stock   = $entries;

            $data['entries'] = $entries;
            $data['exits']   = $exits;
            $data['stock']   = $stock;

            return $this->model->create($data);
        });
    }

    /**
     * Actualiza un material calculando su stock restante con bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return Material|null
     */
    public function update($id, array $data): ?Material
    {
        return DB::transaction(function () use ($id, $data) {
            $material = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$material) {
                return null;
            }

            $entries = (float) (array_key_exists('entries', $data) ? $data['entries'] : $material->entries);
            $exits   = (float) (array_key_exists('exits', $data) ? $data['exits'] : $material->exits);
            $stock   = max(0.0, $entries - $exits);

            $data['entries'] = $entries;
            $data['exits']   = $exits;
            $data['stock']   = $stock;

            $material->update($data);
            return $material->fresh();
        });
    }

    /**
     * Elimina un material de laboratorio con bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $material = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$material) {
                return false;
            }

            return (bool) $material->delete();
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
