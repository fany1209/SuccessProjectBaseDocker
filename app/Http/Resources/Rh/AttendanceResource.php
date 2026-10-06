<?php

namespace App\Http\Resources\Rh;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'fecha' => $this->fecha instanceof \DateTimeInterface ? $this->fecha->format('Y-m-d') : (string)$this->fecha,
            'entrada' => $this->entrada,
            'salida_comida' => $this->salida_comida,
            'regreso_comida' => $this->regreso_comida,
            'salida_final' => $this->salida_final,
            'tipo' => $this->tipo ?? 'Normal',
            'comentario' => $this->comentario,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
