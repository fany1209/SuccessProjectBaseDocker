<?php

namespace App\Http\Resources\Reagent;

use Illuminate\Http\Resources\Json\JsonResource;

class ReagentResource extends JsonResource
{
    /**
     * Transforma el recurso de reactivo en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'         => (int) $this->id,
            'code'       => (string) $this->code,
            'name'       => (string) $this->name,
            'entries'    => (float) ($this->entries ?? 0.0),
            'exits'      => (float) ($this->exits ?? 0.0),
            'stock'      => (float) ($this->stock ?? 0.0),
            'um'         => $this->um !== null ? (string) $this->um : null,
            'brand'      => $this->brand !== null ? (string) $this->brand : null,
            'color'      => $this->color !== null ? (string) $this->color : null,
            'created_at' => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at' => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
