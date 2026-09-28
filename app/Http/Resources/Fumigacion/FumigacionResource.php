<?php

namespace App\Http\Resources\Fumigacion;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FumigacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'proveedor'         => $this->proveedor,
            'fecha_programada'  => $this->fecha_programada ? Carbon::parse($this->fecha_programada)->format('Y-m-d H:i:s') : null,
            'metodo_aplicacion' => $this->metodo_aplicacion,
            'estado'            => $this->estado,
            'observaciones'     => $this->observaciones,
            'created_at'        => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at'        => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
