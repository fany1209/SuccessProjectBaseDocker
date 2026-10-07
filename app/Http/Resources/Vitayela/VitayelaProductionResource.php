<?php

namespace App\Http\Resources\Vitayela;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitayelaProductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'vitayela_production_id' => $this->vitayela_production_id,
            'fecha_preparacion' => $this->fecha_preparacion,
            'kg_preparados' => $this->kg_preparados !== null ? (float)$this->kg_preparados : null,
            'fecha_ensacado' => $this->fecha_ensacado,
            'kg_ensacados' => $this->kg_ensacados !== null ? (float)$this->kg_ensacados : null,
            'num_sacos' => $this->num_sacos !== null ? (int)$this->num_sacos : null,
            'descripcion' => $this->descripcion,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
