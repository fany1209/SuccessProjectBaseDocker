<?php

namespace App\Http\Resources\Laboratory;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSampleRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        $productNames = $this->relationLoaded('items')
            ? $this->items->pluck('product.name')->filter()->implode(', ')
            : '';

        if (empty($productNames) && $this->product_id) {
            $productNames = optional($this->product)->name ?? '';
        }

        return [
            'id'                       => (int) $this->id,
            'folio'                    => $this->folio,
            'fecha_solicitud'          => $this->fecha_solicitud ? (is_string($this->fecha_solicitud) ? $this->fecha_solicitud : $this->fecha_solicitud->format('Y-m-d')) : null,
            'fecha_recoleccion'        => $this->fecha_recoleccion ? (is_string($this->fecha_recoleccion) ? $this->fecha_recoleccion : $this->fecha_recoleccion->format('Y-m-d')) : null,
            'customer_id'              => $this->customer_id ? (int) $this->customer_id : null,
            'cliente_nombre'           => $this->cliente_nombre,
            'cliente_direccion'        => $this->cliente_direccion,
            'cliente_correo'           => $this->cliente_correo,
            'cliente_telefono'         => $this->cliente_telefono,
            'cliente_estatus'          => $this->cliente_estatus,
            'personal_seguimiento'     => $this->personal_seguimiento,
            'entrega_paqueteria'       => (bool) $this->entrega_paqueteria,
            'entrega_personal_empresa' => (bool) $this->entrega_personal_empresa,
            'entrega_recoleccion_planta' => (bool) $this->entrega_recoleccion_planta,
            'entrega_otro'             => (bool) $this->entrega_otro,
            'entrega_otro_txt'         => $this->entrega_otro_txt,
            'paq_nombre'               => $this->paq_nombre,
            'paq_guia'                 => $this->paq_guia,
            'observaciones'            => $this->observaciones,
            'solicitante_nombre'       => $this->solicitante_nombre,
            'status'                   => (int) ($this->status ?? 0),
            'producto_nombre'          => $productNames ?: 'N/A',
            'items'                    => $this->relationLoaded('items') ? $this->items : [],
            'created_at'               => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'               => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
