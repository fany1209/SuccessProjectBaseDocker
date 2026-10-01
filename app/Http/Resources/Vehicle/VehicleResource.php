<?php

namespace App\Http\Resources\Vehicle;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transforma el recurso de vehículo en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'vehicle_id'        => (int) $this->vehicle_id,
            'id'                => (int) $this->vehicle_id,
            'type'              => (string) $this->type,
            'unit_number'       => $this->unit_number !== null ? (string) $this->unit_number : null,
            'plate'             => (string) $this->plate,
            'color'             => $this->color !== null ? (string) $this->color : null,
            'transport_line_id' => (int) $this->transport_line_id,
            'name'              => $this->name ?? optional($this->transportLine)->name ?? '—',
            'created_at'        => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'        => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
