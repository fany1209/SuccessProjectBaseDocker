<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Resources\Json\JsonResource;

class QuarantineResource extends JsonResource
{
    /**
     * Transforma el recurso de cuarentena en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'quarantine_id' => (int) $this->quarantine_id,
            'id'            => (int) $this->quarantine_id,
            'inventory_id'  => (int) $this->inventory_id,
            'quantity'      => (float) $this->quantity,
            'notes'         => (string) $this->notes,
            'inventory'     => $this->whenLoaded('inventory', function () {
                return [
                    'inventory_id' => (int) $this->inventory->inventory_id,
                    'batch'        => (string) $this->inventory->batch,
                    'stock'        => (float) $this->inventory->stock,
                ];
            }),
            'created_at'    => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'    => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
