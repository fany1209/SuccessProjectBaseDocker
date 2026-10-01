<?php

namespace App\Http\Resources\Warehouse;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductionMaterialRequestResource extends JsonResource
{
    /**
     * Transforma la solicitud de material de producción en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'             => (int) $this->id,
            'area'           => (string) $this->area,
            'applicant_name' => (string) $this->applicant_name,
            'status'         => (string) $this->status,
            'comments'       => $this->comments !== null ? (string) $this->comments : null,
            'items'          => $this->relationLoaded('items') ? $this->items->map(function ($item) {
                return [
                    'id'           => (int) $item->id,
                    'request_id'   => (int) $item->request_id,
                    'product_name' => (string) $item->product_name,
                    'quantity'     => (float) $item->quantity,
                ];
            }) : [],
            'created_at'     => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'     => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
