<?php

namespace App\Http\Resources\RecursosHumanos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CursoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fecha' => $this->fecha instanceof \DateTimeInterface ? $this->fecha->format('Y-m-d') : (string)$this->fecha,
            'sede' => $this->sede,
            'horario' => $this->horario,
            'curso' => $this->curso,
            'objetivo' => $this->objetivo,
            'asistentes' => $this->asistentes ?? [],
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
