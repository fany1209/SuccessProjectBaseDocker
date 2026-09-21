<?php

namespace App\Http\Resources\Certificate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'folio'              => $this->folio,
            'fecha'              => $this->fecha?->format('Y-m-d') ?? $this->fecha,
            'cliente'            => $this->cliente,
            'producto'           => $this->producto,
            'lote'               => $this->lote,
            'cantidad'           => $this->cantidad,
            'no_tarimas'         => $this->no_tarimas,
            'fecha_salida_cedis' => $this->fecha_salida_cedis?->format('Y-m-d') ?? $this->fecha_salida_cedis,
            'certificado_tarima' => $this->certificado_tarima,
            'muestra_o_pf'       => $this->muestra_o_pf,
            'created_at'         => $this->created_at?->format('d-m-Y H:i:s'),
            'updated_at'         => $this->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
