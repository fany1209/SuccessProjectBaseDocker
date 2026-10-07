<?php

namespace App\Http\Resources\Vitayela;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitayelaMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'movement_id' => $this->movement_id,
            'vitayela_inventory_id' => $this->vitayela_inventory_id,
            'tipo' => $this->tipo,
            'cantidad' => $this->cantidad !== null ? (float)$this->cantidad : null,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
