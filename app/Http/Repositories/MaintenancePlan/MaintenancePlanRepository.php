<?php

namespace App\Http\Repositories\MaintenancePlan;

use App\Models\ChecklistTemplateItem;
use App\Models\MaintenancePlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MaintenancePlanRepository
{
    protected $plan;

    public function __construct(MaintenancePlan $plan)
    {
        $this->plan = $plan;
    }

    public function all(): Collection
    {
        return $this->plan->with(['equipment', 'checklistItems'])->get();
    }

    public function find($id): ?MaintenancePlan
    {
        return $this->plan->with(['equipment', 'checklistItems', 'maintenanceRecords'])->find($id);
    }

    public function create(array $data, array $checklistItems = []): MaintenancePlan
    {
        return DB::transaction(function () use ($data, $checklistItems) {
            $plan = $this->plan->create($data);

            if (!empty($checklistItems)) {
                foreach ($checklistItems as $index => $itemDescription) {
                    if (!empty($itemDescription)) {
                        ChecklistTemplateItem::create([
                            'maintenance_plan_id' => $plan->id,
                            'description'         => $itemDescription,
                            'order'               => $index,
                        ]);
                    }
                }
            }

            return $plan->load(['equipment', 'checklistItems']);
        });
    }

    public function update($id, array $data, ?array $checklistItems = null): ?MaintenancePlan
    {
        return DB::transaction(function () use ($id, $data, $checklistItems) {
            $plan = $this->plan->lockForUpdate()->find($id);
            if (!$plan) {
                return null;
            }

            $plan->update($data);

            if ($checklistItems !== null) {
                $plan->checklistItems()->delete();
                foreach ($checklistItems as $index => $itemDescription) {
                    if (!empty($itemDescription)) {
                        ChecklistTemplateItem::create([
                            'maintenance_plan_id' => $plan->id,
                            'description'         => $itemDescription,
                            'order'               => $index,
                        ]);
                    }
                }
            }

            return $plan->load(['equipment', 'checklistItems']);
        });
    }

    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $plan = $this->plan->lockForUpdate()->find($id);
            if (!$plan || $this->hasMaintenanceRecords($id)) {
                return false;
            }

            $plan->checklistItems()->delete();
            return (bool) $plan->delete();
        });
    }

    public function hasMaintenanceRecords($id): bool
    {
        $plan = $this->plan->find($id);
        return $plan ? $plan->maintenanceRecords()->exists() : false;
    }
}
