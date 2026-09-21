<?php

namespace App\Http\Repositories\MaintenanceArea;

use App\Models\Area;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MaintenanceAreaRepository
{
    protected $area;

    public function __construct(Area $area)
    {
        $this->area = $area;
    }

    public function all(): Collection
    {
        return $this->area->withCount('equipment')->get();
    }

    public function find($id): ?Area
    {
        return $this->area->withCount('equipment')->find($id);
    }

    public function create(array $data): Area
    {
        return DB::transaction(function () use ($data) {
            return $this->area->create($data);
        });
    }

    public function update($id, array $data): ?Area
    {
        return DB::transaction(function () use ($id, $data) {
            $area = $this->area->lockForUpdate()->find($id);
            if (!$area) {
                return null;
            }
            $area->update($data);
            return $area;
        });
    }

    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $area = $this->area->lockForUpdate()->find($id);
            if (!$area || $area->equipment()->exists()) {
                return false;
            }
            return (bool) $area->delete();
        });
    }

    public function hasEquipment($id): bool
    {
        $area = $this->area->find($id);
        return $area ? $area->equipment()->exists() : false;
    }
}
