<?php

namespace App\Http\Resources\InsumoEntrada;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class InsumoEntradaResource extends JsonResource
{
    /**
     * Transforma el recurso de entrada de insumos en un arreglo tipado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => (int) $this->id,
            'fecha_llegada' => $this->fecha_llegada ? Carbon::parse($this->fecha_llegada)->format('Y-m-d') : null,
            'fecha_salida'  => $this->fecha_salida ? Carbon::parse($this->fecha_salida)->format('Y-m-d') : null,
            'proveedor'     => (string) $this->proveedor,
            'categoria'     => (string) $this->categoria,
            'descripcion'   => $this->descripcion !== null ? (string) $this->descripcion : null,
            'cantidad'      => (float) $this->cantidad,
            'unidad'        => (string) $this->unidad,
            'insumo'        => (string) $this->insumo,
            'lote'          => $this->lote !== null ? (string) $this->lote : null,
            'lote_salida'   => $this->lote_salida !== null ? (string) $this->lote_salida : null,
            'costo'         => (float) ($this->costo ?? 0),
            'moneda'        => (string) ($this->moneda ?? 'MXN'),
            'created_at'    => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'    => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
