<?php

namespace App\Http\Resources\MaintenancePlan;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenancePlanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'equipment_id'    => $this->equipment_id,
            'equipment'       => $this->whenLoaded('equipment', function () {
                return [
                    'id'   => $this->equipment->id,
                    'code' => $this->equipment->code,
                    'name' => $this->equipment->name,
                ];
            }),
            'name'            => $this->name,
            'frequency_days'  => $this->frequency_days,
            'type'            => $this->type,
            'checklist_items' => $this->whenLoaded('checklistItems', function () {
                return $this->checklistItems->map(fn($item) => [
                    'id'          => $item->id,
                    'description' => $item->description,
                    'order'       => $item->order,
                ]);
            }),
            'created_at'      => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'      => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
