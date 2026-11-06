<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionInventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'production_inventory_id' => (int) $this->production_inventory_id,
            'area'                    => $this->area,
            'producto_descripcion'    => $this->producto_descripcion,
            'cantidad'                => (float) $this->cantidad,
            'unidad'                  => $this->unidad,
            'stock_min'               => (float) $this->stock_min,
            'created_at'              => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'              => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}

