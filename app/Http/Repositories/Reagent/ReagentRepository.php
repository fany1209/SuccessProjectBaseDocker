<?php

namespace App\Http\Repositories\Reagent;

use App\Models\Reagent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReagentRepository
{
    /**
     * @var Reagent
     */
    protected Reagent $model;

    /**
     * ReagentRepository constructor.
     *
     * @param Reagent $model
     */
    public function __construct(Reagent $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado de reactivos ordenados por ID descendente con soporte para filtros.
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
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%")
                  ->orWhere('um', 'like', "%{$search}%");
            });
        }

        return $query->latest('id')->get();
    }

    /**
     * Busca un reactivo por su ID.
     *
     * @param int|string $id
     * @return Reagent|null
     */
    public function find($id): ?Reagent
    {
        return $this->model->find($id);
    }

    /**
     * Registra un nuevo reactivo calculando su stock inicial en transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return Reagent
     */
    public function create(array $data): Reagent
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
     * Actualiza un reactivo recalculando su stock restante con bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return Reagent|null
     */
    public function update($id, array $data): ?Reagent
    {
        return DB::transaction(function () use ($id, $data) {
            $reagent = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$reagent) {
                return null;
            }

            $entries = (float) (array_key_exists('entries', $data) ? $data['entries'] : $reagent->entries);
            $exits   = (float) (array_key_exists('exits', $data) ? $data['exits'] : $reagent->exits);
            $stock   = max(0.0, $entries - $exits);

            $data['entries'] = $entries;
            $data['exits']   = $exits;
            $data['stock']   = $stock;

            $reagent->update($data);
            return $reagent->fresh();
        });
    }

    /**
     * Elimina un reactivo de laboratorio con bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $reagent = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$reagent) {
                return false;
            }

            return (bool) $reagent->delete();
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
