<?php

namespace App\Http\Resources\Minuta;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MinutaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_minuta' => $this->id_minuta,
            'fecha_hora' => $this->fecha_hora,
            'lugar' => $this->lugar,
            'tema_general' => $this->tema_general,
            'ponente' => $this->ponente,
            'asistente_nombre' => $this->asistente_nombre,
            'asistente_departamento' => $this->asistente_departamento,
            'tema_tratado' => $this->tema_tratado,
            'acuerdo' => $this->acuerdo,
            'responsable' => $this->responsable,
            'fecha_compromiso' => $this->fecha_compromiso,
            'fecha_cierre' => $this->fecha_cierre,
            'estatus' => $this->estatus,
            'usuario' => $this->whenLoaded('usuario', function () {
                return [
                    'id' => $this->usuario->id,
                    'name' => $this->usuario->name,
                    'email' => $this->usuario->email,
                ];
            }),
            'canUpdate' => true,
            'canDelete' => true,
        ];
    }
}
