<?php

namespace App\Http\Resources\Vitayela;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitayelaInventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'vitayela_inventory_id' => $this->vitayela_inventory_id,
            'producto_descripcion' => $this->producto_descripcion,
            'cantidad' => $this->cantidad !== null ? (float)$this->cantidad : null,
            'unidad' => $this->unidad,
            'stock_min' => $this->stock_min !== null ? (float)$this->stock_min : null,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
