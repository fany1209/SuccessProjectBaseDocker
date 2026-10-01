<?php

namespace App\Http\Resources\Output;

use Illuminate\Http\Resources\Json\JsonResource;

class OutputResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'output_id'            => (int) $this->output_id,
            'id'                   => (int) $this->output_id,
            'operator'             => (string) $this->operator,
            'vendedor'             => (string) $this->vendedor,
            'license_number'       => (string) $this->license_number,
            'security_seal'        => (int) $this->security_seal,
            'security_seal_number' => $this->security_seal_number !== null ? (string) $this->security_seal_number : null,
            'unit_plates'          => (string) $this->unit_plates,
            'trailer_plates'       => $this->trailer_plates !== null ? (string) $this->trailer_plates : null,
            'comments'             => $this->comments !== null ? (string) $this->comments : null,
            'customer_id'          => (int) $this->customer_id,
            'cName'                => $this->cName ?? optional($this->customer)->name ?? '—',
            'transport_line_id'    => (int) $this->transport_line_id,
            'tName'                => $this->tName ?? optional($this->transportLine)->name ?? '—',
            'products'             => $this->relationLoaded('products') ? $this->products->map(function ($p) {
                return [
                    'id'              => $p->pivot->id ?? null,
                    'product_id'      => (int) $p->product_id,
                    'output_id'       => (int) ($p->pivot->output_id ?? $this->output_id),
                    'name'            => (string) $p->name,
                    'unit'            => (string) $p->unit,
                    'quantity'        => (float) ($p->pivot->quantity ?? 0.0),
                    'warehouse_batch' => $p->pivot->warehouse_batch ?? null,
                    'label_batch'     => $p->pivot->label_batch ?? null,
                ];
            }) : [],
            'created_at'           => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'           => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
