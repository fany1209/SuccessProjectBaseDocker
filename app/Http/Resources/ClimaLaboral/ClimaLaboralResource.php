<?php

namespace App\Http\Resources\ClimaLaboral;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClimaLaboralResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                          => $this->id,
            'user_id'                     => $this->user_id,
            'q1_ambiente'                 => (int) $this->q1_ambiente,
            'q2_respeto'                  => (int) $this->q2_respeto,
            'q3_comunicacion_oportuna'    => (int) $this->q3_comunicacion_oportuna,
            'q4_comunicacion_escucha'     => (int) $this->q4_comunicacion_escucha,
            'q5_liderazgo'                => (int) $this->q5_liderazgo,
            'q6_reconocimiento'           => (int) $this->q6_reconocimiento,
            'q7_desarrollo'               => (int) $this->q7_desarrollo,
            'q8_motivacion'               => (int) $this->q8_motivacion,
            'q9_satisfaccion'             => (int) $this->q9_satisfaccion,
            'q10_bienestar_carga'         => (int) $this->q10_bienestar_carga,
            'q11_bienestar_preocupacion'  => (int) $this->q11_bienestar_preocupacion,
            'q12_sugerencias'             => $this->q12_sugerencias,
            'created_at'                  => $this->created_at?->format('d-m-Y H:i:s'),
            'updated_at'                  => $this->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
