<?php

namespace App\Http\Resources\MaintenanceEquipment;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceEquipmentResource extends JsonResource
{
    /**
     * Transforma el recurso de equipo en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'code'       => $this->code,
            'name'       => $this->name,
            'area_id'    => $this->area_id,
            'area'       => $this->whenLoaded('area', function () {
                return [
                    'id'   => $this->area->id,
                    'name' => $this->area->name,
                ];
            }),
            'is_active'  => (bool) $this->is_active,
            'created_at' => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at' => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
