<?php

namespace App\Http\Resources\Fertil;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FertilInventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'fertil_inventory_id'  => (int) $this->fertil_inventory_id,
            'producto_descripcion' => $this->producto_descripcion,
            'cantidad'             => (float) $this->cantidad,
            'unidad'               => $this->unidad,
            'stock_min'            => (float) $this->stock_min,
            'created_at'           => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'           => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
