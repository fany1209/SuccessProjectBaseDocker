<?php

namespace App\Http\Resources\Contrato;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContratoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'mes_1' => $this->mes_1 ? asset($this->mes_1) : null,
            'mes_1_path' => $this->mes_1,
            'mes_2' => $this->mes_2 ? asset($this->mes_2) : null,
            'mes_2_path' => $this->mes_2,
            'mes_3' => $this->mes_3 ? asset($this->mes_3) : null,
            'mes_3_path' => $this->mes_3,
            'indefinido' => $this->indefinido ? asset($this->indefinido) : null,
            'indefinido_path' => $this->indefinido,
            'confidencialidad' => $this->confidencialidad ? asset($this->confidencialidad) : null,
            'confidencialidad_path' => $this->confidencialidad,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'tipo_empleado' => $this->user->tipo_empleado,
                ];
            }),
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
