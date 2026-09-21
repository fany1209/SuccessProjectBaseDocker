<?php

namespace App\Http\Resources\CuentasPorPagar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CxpPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'cxp_detail_id' => $this->cxp_detail_id,
            'amount'        => (float) $this->amount,
            'date'          => $this->date,
            'comprobante'   => $this->comprobante ? asset($this->comprobante) : null,
            'notas'         => $this->notas,
            'banco'         => $this->banco,
            'metodo_pago'   => $this->metodo_pago,
            'user_id'       => $this->user_id,
            'user_name'     => $this->user_name ?? ($this->relationLoaded('user') && $this->user ? $this->user->name : null),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
