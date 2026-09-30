<?php

namespace App\Http\Repositories\Operator;

use App\Models\Operator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OperatorRepository
{
    /**
     * @var Operator
     */
    protected Operator $model;

    /**
     * OperatorRepository constructor.
     *
     * @param Operator $model
     */
    public function __construct(Operator $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado de operadores con soporte para filtros.
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
                  ->orWhere('license', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Busca un operador por su ID.
     *
     * @param int|string $id
     * @return Operator|null
     */
    public function find($id): ?Operator
    {
        return $this->model->where('operator_id', $id)->first();
    }

    /**
     * Registra un nuevo operador en transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return Operator
     */
    public function create(array $data): Operator
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    /**
     * Actualiza un operador con bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return Operator|null
     */
    public function update($id, array $data): ?Operator
    {
        return DB::transaction(function () use ($id, $data) {
            $operator = $this->model->newQuery()
                ->where('operator_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$operator) {
                return null;
            }

            $operator->update($data);
            return $operator->fresh();
        });
    }

    /**
     * Elimina un operador con bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $operator = $this->model->newQuery()
                ->where('operator_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$operator) {
                return false;
            }

            return (bool) $operator->delete();
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
