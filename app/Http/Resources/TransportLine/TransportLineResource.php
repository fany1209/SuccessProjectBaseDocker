<?php

namespace App\Http\Resources\TransportLine;

use Illuminate\Http\Resources\Json\JsonResource;

class TransportLineResource extends JsonResource
{
    /**
     * Transforma el recurso de línea de transporte en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'transport_line_id' => (int) $this->transport_line_id,
            'id'                => (int) $this->transport_line_id,
            'name'              => (string) $this->name,
            'created_at'        => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'        => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
