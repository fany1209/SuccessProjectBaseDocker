<?php

namespace App\Http\Repositories\ItEquipment;

use App\Models\ItEquipment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ItEquipmentRepository
{
    /**
     * @var ItEquipment
     */
    protected ItEquipment $model;

    /**
     * ItEquipmentRepository constructor.
     *
     * @param ItEquipment $model
     */
    public function __construct(ItEquipment $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene todos los registros con filtros opcionales.
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
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('responsible', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (!empty($filters['article'])) {
            $query->where('article', $filters['article']);
        }

        return $query->latest('id')->get();
    }

    /**
     * Busca un equipo de TI por su ID.
     *
     * @param int|string $id
     * @return ItEquipment|null
     */
    public function find($id): ?ItEquipment
    {
        return $this->model->find($id);
    }

    /**
     * Crea un nuevo registro de equipo de TI bajo una transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return ItEquipment
     */
    public function create(array $data): ItEquipment
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    /**
     * Actualiza un equipo de TI usando bloqueo pesimista contra condiciones de carrera.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return ItEquipment|null
     */
    public function update($id, array $data): ?ItEquipment
    {
        return DB::transaction(function () use ($id, $data) {
            $equipment = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$equipment) {
                return null;
            }

            $equipment->update($data);
            return $equipment->fresh();
        });
    }

    /**
     * Elimina un equipo de TI usando bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $equipment = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$equipment) {
                return false;
            }

            return (bool) $equipment->delete();
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
