<?php

namespace App\Http\Resources\Finance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancePaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'empresa'       => $this->empresa,
            'cantidad'      => (float) $this->cantidad,
            'motivo'        => $this->motivo,
            'banco'         => $this->banco,
            'factura'       => $this->factura,
            'fecha_factura' => $this->fecha_factura ? (is_string($this->fecha_factura) ? $this->fecha_factura : $this->fecha_factura->format('Y-m-d')) : null,
            'fecha_pago'    => $this->fecha_pago ? (is_string($this->fecha_pago) ? $this->fecha_pago : $this->fecha_pago->format('Y-m-d')) : null,
            'estatus'       => $this->estatus,
            'comentarios'   => $this->comentarios,
            'semana'        => (int) $this->semana,
            'anio'          => (int) $this->anio,
            'terminacion'   => $this->terminacion,
            'efectivo'      => $this->efectivo,
            'created_at'    => $this->created_at ? (is_string($this->created_at) ? $this->created_at : $this->created_at->format('d-m-Y H:i:s')) : null,
            'updated_at'    => $this->updated_at ? (is_string($this->updated_at) ? $this->updated_at : $this->updated_at->format('d-m-Y H:i:s')) : null,
        ];
    }
}
