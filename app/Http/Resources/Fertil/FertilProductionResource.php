<?php

namespace App\Http\Resources\Fertil;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FertilProductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'fertil_production_id' => (int) $this->fertil_production_id,
            'fecha_preparacion'    => $this->fecha_preparacion ? (is_string($this->fecha_preparacion) ? $this->fecha_preparacion : $this->fecha_preparacion->format('Y-m-d')) : null,
            'kg_preparados'        => (float) $this->kg_preparados,
            'fecha_ensacado'       => $this->fecha_ensacado ? (is_string($this->fecha_ensacado) ? $this->fecha_ensacado : $this->fecha_ensacado->format('Y-m-d')) : null,
            'kg_ensacados'         => (float) $this->kg_ensacados,
            'num_sacos'            => (int) $this->num_sacos,
            'descripcion'          => $this->descripcion,
            'created_at'           => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'           => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
