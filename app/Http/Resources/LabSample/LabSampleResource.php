<?php

namespace App\Http\Resources\LabSample;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LabSampleResource extends JsonResource
{
    /**
     * Transforma el recurso de muestra de laboratorio en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $toYmd = function ($date) {
            if (!$date) return null;
            try {
                return Carbon::parse($date)->toDateString();
            } catch (\Throwable $e) {
                return null;
            }
        };

        return [
            'id'                => (int) $this->id,
            'folio'             => (string) $this->folio,
            'tipo_muestra'      => $this->tipo_muestra !== null ? (string) $this->tipo_muestra : null,
            'producto'          => $this->producto !== null ? (string) $this->producto : null,
            'sku'               => $this->sku !== null ? (string) $this->sku : null,
            'proveedor'         => $this->proveedor !== null ? (string) $this->proveedor : null,
            'lote'              => isset($this->lote) ? (string) $this->lote : null,
            'ubicacion_stock'   => $this->ubicacion_stock !== null ? (string) $this->ubicacion_stock : null,
            'stock_inicial'     => $this->stock_inicial !== null ? (float) $this->stock_inicial : 0.0,
            'cantidad_salida'   => $this->cantidad_salida !== null ? (float) $this->cantidad_salida : 0.0,
            'stock_final'       => $this->stock_final !== null ? (float) $this->stock_final : 0.0,
            'fecha_entrada'     => $this->fecha_entrada ? $toYmd($this->fecha_entrada) : null,
            'fecha_salida'      => $this->fecha_salida ? $toYmd($this->fecha_salida) : null,
            'fecha_entrada_raw' => $this->fecha_entrada ? $toYmd($this->fecha_entrada) : null,
            'fecha_salida_raw'  => $this->fecha_salida ? $toYmd($this->fecha_salida) : null,
            'status'            => (string) ($this->status ?? 'Fuera de laboratorio'),
            'motivo_salida'     => $this->motivo_salida !== null ? (string) $this->motivo_salida : null,
            'solicitante'       => $this->solicitante !== null ? (string) $this->solicitante : null,
            'recolector'        => $this->recolector !== null ? (string) $this->recolector : null,
            'cliente'           => $this->cliente !== null ? (string) $this->cliente : null,
            'created_at'        => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'        => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
