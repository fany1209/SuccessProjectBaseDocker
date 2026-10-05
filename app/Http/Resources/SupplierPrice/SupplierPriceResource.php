<?php

namespace App\Http\Resources\SupplierPrice;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'factura_id'       => $this->factura_id,
            'insumo'           => $this->insumo,
            'clave_sat'        => $this->clave_sat,
            'proveedor'        => $this->proveedor,
            'precio'           => (float) $this->precio,
            'tiene_iva'        => (bool) $this->tiene_iva,
            'moneda'           => $this->moneda,
            'fecha_cotizacion' => $this->fecha_cotizacion ? Carbon::parse($this->fecha_cotizacion)->format('d/m/Y') : null,
            'created_at'       => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'       => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
