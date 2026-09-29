<?php

namespace App\Http\Repositories\ItInspection;

use App\Models\ItInspection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ItInspectionRepository
{
    /**
     * @var ItInspection
     */
    protected ItInspection $model;

    /**
     * ItInspectionRepository constructor.
     *
     * @param ItInspection $model
     */
    public function __construct(ItInspection $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado de inspecciones de TI aplicando filtros de búsqueda.
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
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        return $query->latest('id')->get();
    }

    /**
     * Busca una inspección de TI por su ID.
     *
     * @param int|string $id
     * @return ItInspection|null
     */
    public function find($id): ?ItInspection
    {
        return $this->model->find($id);
    }

    /**
     * Genera el siguiente folio correlativo de inspección.
     *
     * @return string
     */
    public function generateNextFolio(): string
    {
        return ItInspection::generateFolio();
    }

    /**
     * Registra una nueva inspección bajo una transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return ItInspection
     */
    public function create(array $data): ItInspection
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['folio'])) {
                $data['folio'] = $this->generateNextFolio();
            }

            return $this->model->create($data);
        });
    }

    /**
     * Actualiza una inspección existente aplicando bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return ItInspection|null
     */
    public function update($id, array $data): ?ItInspection
    {
        return DB::transaction(function () use ($id, $data) {
            $inspection = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$inspection) {
                return null;
            }

            $inspection->update($data);
            return $inspection->fresh();
        });
    }

    /**
     * Elimina una inspección de TI usando bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $inspection = $this->model->newQuery()->lockForUpdate()->find($id);

            if (!$inspection) {
                return false;
            }

            return (bool) $inspection->delete();
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
