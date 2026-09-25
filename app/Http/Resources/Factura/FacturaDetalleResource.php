<?php

namespace App\Http\Resources\Factura;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacturaDetalleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'detalle_id'               => (int) $this->detalle_id,
            'factura_id'               => (int) $this->factura_id,
            'producto'                 => $this->producto,
            'clave_sat'                => $this->clave_sat,
            'unidad'                   => $this->unidad,
            'cantidad'                 => (float) $this->cantidad,
            'precio_unitario'          => (float) $this->precio_unitario,
            'precio'                   => (float) $this->precio,
            'subtotal'                 => (float) $this->subtotal,
            'aplica_iva'               => (bool) $this->aplica_iva,
            'iva_porcentaje'           => (float) $this->iva_porcentaje,
            'otro_impuesto'            => (float) $this->otro_impuesto_porcentaje,
            'otro_impuesto_porcentaje' => (float) $this->otro_impuesto_porcentaje,
            'impuesto_total'           => (float) $this->impuesto_total,
            'traslado'                 => (float) $this->traslado,
            'retencion'                => (float) $this->retencion,
            'descuento'                => (float) $this->descuento,
            'isr'                      => (float) $this->isr,
            'ilc'                      => (float) $this->ilc,
        ];
    }
}
