<?php

namespace App\Http\Resources\Laboratory;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LaboratorySampleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => (int) $this->id,
            'folio'           => $this->folio,
            'tipo_muestra'    => $this->tipo_muestra,
            'proveedor'       => $this->proveedor,
            'sku'             => $this->sku,
            'producto'        => $this->producto,
            'stock_inicial'   => $this->stock_inicial !== null ? (float) $this->stock_inicial : null,
            'presentacion'    => $this->presentacion,
            'ubicacion_stock' => $this->ubicacion_stock,
            'fecha_entrada'   => $this->fecha_entrada ? Carbon::parse($this->fecha_entrada)->format('Y-m-d') : null,
            'fecha_salida'    => $this->fecha_salida ? Carbon::parse($this->fecha_salida)->format('Y-m-d') : null,
            'cantidad_salida' => $this->cantidad_salida !== null ? (float) $this->cantidad_salida : null,
            'motivo_salida'   => $this->motivo_salida,
            'solicitante'     => $this->solicitante,
            'recolector'      => $this->recolector,
            'cliente'         => $this->cliente,
            'status'          => $this->status,
            'created_at'      => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'      => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
