<?php

namespace App\Http\Resources\Quality;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionWResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'fecha_inspeccion' => $this->fecha_inspeccion ? $this->fecha_inspeccion : null,
            'inspector'        => $this->inspector,
            'hora_turno'       => $this->hora_turno,
            'turno'            => $this->turno,
            'area'             => $this->area,
            'area_otro'        => $this->area_otro,
            'responsable'      => $this->responsable,
            'comentarios'      => $this->comentarios,
            'comentarios_q'    => $this->comentarios_q,
            'status'           => (bool) $this->status,
            'observaciones'    => $this->whenLoaded('observaciones', function () {
                return $this->observaciones->map(function ($obs) {
                    return [
                        'id'             => $obs->id,
                        'name'           => $obs->name,
                        'rev'            => $obs->rev,
                        'fecha'          => $obs->fecha,
                        'ubicacion'      => $obs->ubicacion,
                        'evidencia_path' => $obs->evidencia_path,
                        'evidencia_url'  => $obs->evidencia_path ? asset($obs->evidencia_path) : null,
                        'ev_corr_path'   => $obs->ev_corr_path,
                        'ev_corr_url'    => $obs->ev_corr_path ? asset($obs->ev_corr_path) : null,
                    ];
                });
            }),
            'created_at'       => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'       => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
