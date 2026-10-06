<?php

namespace App\Http\Resources\Nomina;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NominaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => (int) $this->id,
            'nombre'              => $this->nombre,
            'curp'                => $this->curp ?: '—',
            'rfc'                 => $this->rfc ?: '—',
            'nss'                 => $this->nss ?: '—',
            'puesto'              => $this->puesto,
            'fecha_ingreso'       => $this->fecha_ingreso ? $this->fecha_ingreso->format('Y-m-d') : null,
            'fecha_baja'          => $this->fecha_baja ? $this->fecha_baja->format('Y-m-d') : null,
            'edad'                => $this->calculated_edad !== null ? $this->calculated_edad : ($this->edad ?? '—'),
            'antiguedad'          => $this->calculated_antiguedad ?: '—',
            'sexo'                => $this->sexo ?: '—',
            'estado_civil'        => $this->estado_civil ?: '—',
            'fecha_nacimiento'    => $this->fecha_nacimiento ? $this->fecha_nacimiento->format('Y-m-d') : null,
            'nombre_beneficiario' => $this->nombre_beneficiario ?: '—',
            'parentesco'          => $this->parentesco ?: '—',
            'domicilio'           => $this->domicilio ?: '—',
            'cp'                  => $this->cp ?: '—',
            'telefono'            => $this->telefono ?: '—',
            'correo'              => $this->correo ?: '—',
            'estatus'             => $this->estatus,
            'user_id'             => $this->user_id,
            'user'                => $this->user?->name ?? null,
            'created_at'          => $this->created_at ? $this->created_at->format('d/m/Y') : '—',
            'updated_at'          => $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : '—',
        ];
    }
}
