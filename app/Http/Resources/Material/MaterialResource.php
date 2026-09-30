<?php

namespace App\Http\Resources\Material;

use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    /**
     * Transforma el recurso de material de laboratorio en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'         => (int) $this->id,
            'name'       => (string) $this->name,
            'entries'    => (float) ($this->entries ?? 0.0),
            'exits'      => (float) ($this->exits ?? 0.0),
            'stock'      => (float) ($this->stock ?? 0.0),
            'um'         => $this->um !== null ? (string) $this->um : null,
            'brand'      => $this->brand !== null ? (string) $this->brand : null,
            'created_at' => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at' => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
