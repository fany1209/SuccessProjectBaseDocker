<?php

namespace App\Http\Resources\CuentasPorCobrar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CxcPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cxc_detail_id' => $this->cxc_detail_id,
            'amount' => (float) $this->amount,
            'date' => $this->date ? \Carbon\Carbon::parse($this->date)->format('Y-m-d') : null,
            'comprobante' => $this->comprobante,
            'comprobante_url' => $this->comprobante ? asset('storage/comprobantes/' . $this->comprobante) : null,
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
