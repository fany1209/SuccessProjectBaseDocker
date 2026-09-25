<?php

namespace App\Http\Resources\Factura;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacturaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'factura_id'      => (int) $this->factura_id,
            'empresa'         => $this->empresa,
            'tipo_documento'  => $this->tipo_documento,
            'folio_factura'   => $this->folio_factura,
            'subtotal'        => (float) $this->subtotal,
            'descuento_total' => (float) $this->descuento_total,
            'iva'             => (float) $this->iva,
            'traslado_total'  => (float) $this->traslado_total,
            'retencion_total' => (float) $this->retencion_total,
            'total'           => (float) $this->total,
            'moneda'          => $this->moneda ?? 'MXN',
            'tipo_cambio'     => (float) ($this->tipo_cambio ?? 1.0),
            'fecha_factura'   => $this->fecha_factura ? (is_string($this->fecha_factura) ? $this->fecha_factura : $this->fecha_factura->format('Y-m-d')) : null,
            'insumo'          => $this->insumo,
            'departamento'    => $this->departamento,
            'descripcion'     => $this->descripcion,
            'detalles'        => FacturaDetalleResource::collection($this->whenLoaded('detalles')),
            'created_at'      => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'      => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
