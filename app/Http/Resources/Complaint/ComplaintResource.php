<?php

namespace App\Http\Resources\Complaint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fecha' => $this->fecha ? $this->fecha->format('Y-m-d') : null,
            'tipo' => $this->tipo,
            'motivos' => $this->motivos ?? [],
            'motivo_otro' => $this->motivo_otro,
            'descripcion' => $this->descripcion,
            'evidencias' => $this->evidencias ?? [],
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
