<?php

namespace App\Http\Resources\Maintenance;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MaintenanceRecordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'code'                => $this->code,
            'equipment_id'        => $this->equipment_id,
            'equipment'           => $this->whenLoaded('equipment', function () {
                return [
                    'id'   => $this->equipment->id,
                    'code' => $this->equipment->code,
                    'name' => $this->equipment->name,
                ];
            }),
            'maintenance_plan_id' => $this->maintenance_plan_id,
            'maintenance_plan'    => $this->whenLoaded('maintenancePlan', function () {
                return [
                    'id'             => $this->maintenancePlan->id,
                    'name'           => $this->maintenancePlan->name,
                    'type'           => $this->maintenancePlan->type,
                    'frequency_days' => $this->maintenancePlan->frequency_days,
                ];
            }),
            'scheduled_date'      => optional($this->scheduled_date)->format('d/m/Y'),
            'status'              => $this->status,
            'printed_at'          => optional($this->printed_at)->format('d-m-Y H:i:s'),
            'completed_at'        => optional($this->completed_at)->format('d-m-Y H:i:s'),
            'evidence_file'       => $this->evidence_file ? url(Storage::url($this->evidence_file)) : null,
            'created_at'          => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'          => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
