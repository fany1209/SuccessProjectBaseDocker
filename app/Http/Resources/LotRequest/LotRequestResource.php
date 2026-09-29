<?php

namespace App\Http\Resources\LotRequest;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LotRequestResource extends JsonResource
{
    /**
     * Transforma el recurso de petición de lote en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'           => (int) $this->id,
            'department'   => (string) $this->department,
            'requested_at' => $this->requested_at ? Carbon::parse($this->requested_at)->format('Y-m-d H:i') : null,
            'status'       => (string) ($this->status ?? 'pendiente'),
            'comments'     => $this->comments !== null ? (string) $this->comments : null,
            'product'      => $this->product !== null ? (string) $this->product : null,
            'quantity'     => $this->quantity !== null ? (string) $this->quantity : null,
            'provider'     => $this->provider !== null ? (string) $this->provider : null,
            'collector'    => $this->collector !== null ? (string) $this->collector : null,
            'sector'       => $this->sector !== null ? (string) $this->sector : null,
            'sku'          => $this->sku !== null ? (string) $this->sku : null,
            'batch'        => $this->batch !== null ? (string) $this->batch : null,
            'created_at'   => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'   => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
