<?php

namespace App\Http\Resources\CuentasPorPagar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CxpDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'factura_id'     => $this->factura_id,
            'semana'         => $this->semana,
            'anio'           => $this->anio,
            'fecha_pago'     => $this->fecha_pago,
            'estatus'        => $this->estatus,
            'is_canceled'    => (bool) $this->is_canceled,
            'pdf_path'       => $this->pdf_path ? asset($this->pdf_path) : null,
            'xml_path'       => $this->xml_path ? asset($this->xml_path) : null,
            'comentarios'    => $this->comentarios,
            'comentario_img' => $this->comentario_img ? asset($this->comentario_img) : null,
            'payments'       => CxpPaymentResource::collection($this->whenLoaded('payments')),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
