<?php

namespace App\Http\Resources\Tweak;

use Illuminate\Http\Resources\Json\JsonResource;

class TweakResource extends JsonResource
{
    /**
     * Transforma el recurso de ajuste de inventario en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'tweak_id'     => (int) $this->tweak_id,
            'id'           => (int) $this->tweak_id,
            'type'         => (string) $this->type,
            'quantity'     => (float) $this->quantity,
            'comments'     => $this->comments !== null ? (string) $this->comments : null,
            'inventory_id' => (int) $this->inventory_id,
            'user_id'      => $this->user_id !== null ? (int) $this->user_id : null,
            'user_name'    => optional($this->user)->name,
            'inventory'    => $this->relationLoaded('inventory') && $this->inventory ? [
                'batch'        => $this->inventory->batch,
                'stock'        => (float) $this->inventory->stock,
                'product_name' => optional($this->inventory->product)->name,
            ] : null,
            'created_at'   => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'   => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
