<?php

namespace App\Http\Repositories\MaintenanceEquipment;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MaintenanceEquipmentRepository
{
    protected $equipment;

    public function __construct(Equipment $equipment)
    {
        $this->equipment = $equipment;
    }

    public function all(): Collection
    {
        return $this->equipment->with('area')->get();
    }

    public function find($id): ?Equipment
    {
        return $this->equipment->with(['area', 'maintenancePlans', 'maintenanceRecords'])->find($id);
    }

    public function create(array $data): Equipment
    {
        return DB::transaction(function () use ($data) {
            return $this->equipment->create($data);
        });
    }

    public function update($id, array $data): ?Equipment
    {
        return DB::transaction(function () use ($id, $data) {
            $equipment = $this->equipment->lockForUpdate()->find($id);
            if (!$equipment) {
                return null;
            }
            $equipment->update($data);
            return $equipment;
        });
    }

    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $equipment = $this->equipment->lockForUpdate()->find($id);
            if (!$equipment || $this->hasMaintenanceRecords($id) || $this->hasMaintenancePlans($id)) {
                return false;
            }
            return (bool) $equipment->delete();
        });
    }

    public function hasMaintenanceRecords($id): bool
    {
        $equipment = $this->equipment->find($id);
        return $equipment ? $equipment->maintenanceRecords()->exists() : false;
    }

    public function hasMaintenancePlans($id): bool
    {
        $equipment = $this->equipment->find($id);
        return $equipment ? $equipment->maintenancePlans()->exists() : false;
    }
}
