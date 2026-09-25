<?php

namespace App\Http\Resources\Equipment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => (int) $this->id,
            'internal_code' => $this->internal_code,
            'name'          => $this->name,
            'quantity'      => (float) $this->quantity,
            'brand'         => $this->brand,
            'status'        => $this->status,
            'created_at'    => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'    => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
