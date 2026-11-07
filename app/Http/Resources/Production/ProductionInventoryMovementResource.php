<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionInventoryMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'movement_id'             => (int) $this->movement_id,
            'production_inventory_id' => (int) $this->production_inventory_id,
            'tipo'                    => $this->tipo,
            'cantidad'                => (float) $this->cantidad,
            'created_at'              => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'              => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}

