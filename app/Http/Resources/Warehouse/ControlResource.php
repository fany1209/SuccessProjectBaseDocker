<?php

namespace App\Http\Resources\Warehouse;

use Illuminate\Http\Resources\Json\JsonResource;

class ControlResource extends JsonResource
{
    /**
     * Transforma el recurso de control de temperatura/humedad en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'control_id'   => (int) $this->control_id,
            'id'           => (int) $this->control_id,
            'temperature'  => (float) $this->temperature,
            'humidity'     => (float) $this->humidity,
            'warehouse_id' => (int) $this->warehouse_id,
            'user_id'      => $this->user_id !== null ? (int) $this->user_id : null,
            'host_ip'      => $this->host_ip,
            'host_user'    => $this->host_user,
            'host_name'    => $this->host_name,
            'created_at'   => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'   => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
