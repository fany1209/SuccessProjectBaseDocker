<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'production_record_id' => (int) $this->production_record_id,
            'area'                 => $this->area,
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

