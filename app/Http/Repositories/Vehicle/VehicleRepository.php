<?php

namespace App\Http\Repositories\Vehicle;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class VehicleRepository
{
    /**
     * @var Vehicle
     */
    protected Vehicle $model;

    /**
     * VehicleRepository constructor.
     *
     * @param Vehicle $model
     */
    public function __construct(Vehicle $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado de vehículos con unión a líneas de transporte y soporte para filtros.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery()
            ->select('vehicles.*', 'transport_lines.name as name')
            ->join('transport_lines', 'transport_lines.transport_line_id', '=', 'vehicles.transport_line_id');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('transport_lines.name', 'like', "%{$search}%")
                  ->orWhere('vehicles.plate', 'like', "%{$search}%")
                  ->orWhere('vehicles.type', 'like', "%{$search}%")
                  ->orWhere('vehicles.unit_number', 'like', "%{$search}%")
                  ->orWhere('vehicles.color', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['t_line'])) {
            $query->where('vehicles.transport_line_id', $filters['t_line']);
        }

        return $query->orderBy('vehicles.vehicle_id', 'desc')->get();
    }

    /**
     * Busca un vehículo por su ID junto con su línea de transporte.
     *
     * @param int|string $id
     * @return Vehicle|null
     */
    public function find($id): ?Vehicle
    {
        return $this->model->newQuery()
            ->with('transportLine')
            ->where('vehicle_id', $id)
            ->first();
    }

    /**
     * Registra un nuevo vehículo en transacción ACID.
     *
     * @param array<string, mixed> $data
     * @return Vehicle
     */
    public function create(array $data): Vehicle
    {
        return DB::transaction(function () use ($data) {
            $vehicle = $this->model->create($data);
            $vehicle->load('transportLine');
            return $vehicle;
        });
    }

    /**
     * Actualiza un vehículo con bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return Vehicle|null
     */
    public function update($id, array $data): ?Vehicle
    {
        return DB::transaction(function () use ($id, $data) {
            $vehicle = $this->model->newQuery()
                ->where('vehicle_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$vehicle) {
                return null;
            }

            $vehicle->update($data);
            $vehicle->load('transportLine');
            return $vehicle->fresh(['transportLine']);
        });
    }

    /**
     * Elimina un vehículo con bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $vehicle = $this->model->newQuery()
                ->where('vehicle_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$vehicle) {
                return false;
            }

            return (bool) $vehicle->delete();
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
