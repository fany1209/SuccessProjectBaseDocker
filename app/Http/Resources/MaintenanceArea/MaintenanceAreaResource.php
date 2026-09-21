<?php

namespace App\Http\Resources\MaintenanceArea;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceAreaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => $this->description,
            'equipment_count' => $this->equipment_count ?? ($this->equipment ? $this->equipment->count() : 0),
            'created_at'      => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'      => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
