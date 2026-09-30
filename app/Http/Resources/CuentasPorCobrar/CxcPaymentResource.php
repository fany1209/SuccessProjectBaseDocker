<?php

namespace App\Http\Resources\CuentasPorCobrar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CxcPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $comprobanteUrl = null;
        if ($this->comprobante) {
            if (str_starts_with($this->comprobante, 'http://') || str_starts_with($this->comprobante, 'https://')) {
                $comprobanteUrl = $this->comprobante;
            } else {
                $clean = basename(str_replace('\\', '/', urldecode($this->comprobante)));
                $comprobanteUrl = asset('documentos_finanzas/cxc/comprobantes/' . $clean);
            }
        }

        return [
            'id' => $this->id,
            'cxc_detail_id' => $this->cxc_detail_id,
            'amount' => (float) $this->amount,
            'date' => $this->date ? \Carbon\Carbon::parse($this->date)->format('Y-m-d') : null,
            'comprobante' => $this->comprobante,
            'comprobante_url' => $comprobanteUrl,
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
