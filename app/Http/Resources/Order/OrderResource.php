<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                             => $this->id,
            'user_id'                        => $this->user_id,
            'año'                            => $this->año,
            'semana'                         => $this->semana,
            'empresa'                        => $this->empresa,
            'cantidad'                       => $this->cantidad,
            'producto'                       => $this->producto,
            'po'                             => $this->po,
            'pdf_path'                       => $this->pdf_path,
            'pdf_url'                        => $this->pdf_path ? asset('orders_pdf/' . $this->pdf_path) : null,
            'fecha_de_carga'                 => $this->fecha_de_carga,
            'hora'                           => $this->hora,
            'fecha_de_envio'                 => $this->fecha_de_envio,
            'fecha_requerida_por_el_cliente' => $this->fecha_requerida_por_el_cliente,
            'transporte'                     => $this->transporte,
            'estatus_almacen'                => $this->estatus_almacen,
            'estatus_calidad'                => $this->estatus_calidad,
            'estatus_administrativo'         => $this->estatus_administrativo,
            'documentacion_requerida'        => $this->documentacion_requerida,
            'comentarios'                    => $this->comentarios,
            'items'                          => $this->items ? $this->items->map(function ($item) {
                return [
                    'id'       => $item->id,
                    'producto' => $item->producto,
                    'cantidad' => $item->cantidad,
                ];
            }) : [],
            'user'                           => $this->user ? [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ] : null,
        ];
    }
}
