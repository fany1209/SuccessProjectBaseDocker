<?php

namespace App\Http\Resources\ItEquipment;

use Illuminate\Http\Resources\Json\JsonResource;

class ItEquipmentResource extends JsonResource
{
    /**
     * Transforma el recurso de equipo de TI en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => (int) $this->id,
            'department'    => (string) $this->department,
            'responsible'   => $this->responsible !== null ? (string) $this->responsible : null,
            'article'       => (string) $this->article,
            'brand'         => $this->brand !== null ? (string) $this->brand : null,
            'model'         => $this->model !== null ? (string) $this->model : null,
            'serial_number' => $this->serial_number !== null ? (string) $this->serial_number : null,
            'success_code'  => $this->success_code !== null ? (string) $this->success_code : null,
            'image_url'     => $this->image_url !== null ? (string) $this->image_url : null,
            'created_at'    => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'    => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
