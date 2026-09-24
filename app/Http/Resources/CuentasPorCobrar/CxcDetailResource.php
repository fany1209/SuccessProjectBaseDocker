<?php

namespace App\Http\Resources\CuentasPorCobrar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CxcDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'folio' => $this->folio ?? $this->sale?->folio,
            'cliente_name' => $this->cliente_name ?? null,
            'asesor' => $this->asesor ?? null,
            'documento' => $this->documento,
            'metodo_pago' => $this->metodo_pago,
            'estatus' => $this->estatus,
            'descripcion' => $this->descripcion,
            'fecha_emision' => $this->fecha_emision ?? null,
            'fecha_conclusion' => $this->fecha_conclusion ? \Carbon\Carbon::parse($this->fecha_conclusion)->format('Y-m-d') : null,
            'is_canceled' => (bool) $this->is_canceled,
            'total_venta' => isset($this->total_venta) ? (float) $this->total_venta : 0.0,
            'pagado' => isset($this->pagado) ? (float) $this->pagado : 0.0,
            'saldo' => isset($this->saldo) ? (float) $this->saldo : 0.0,
            'payments' => CxcPaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
