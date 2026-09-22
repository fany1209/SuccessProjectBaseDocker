<?php

namespace App\Http\Resources\Comparative;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComparativeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'folio' => $this->folio,
            'insumo' => $this->insumo,
            'cantidad' => $this->cantidad !== null ? (float) $this->cantidad : null,
            'proveedor' => $this->proveedor,
            'precio_unt' => $this->precio_unt !== null ? (float) $this->precio_unt : 0.0,
            'precio_total' => $this->precio_total !== null ? (float) $this->precio_total : 0.0,
            'imagen' => $this->imagen,
            'descripcion' => $this->descripcion,
            'comentarios' => $this->comentarios,
            'entrega_estimada' => $this->entrega_estimada,
            'link' => $this->link,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
        ];
    }
}
