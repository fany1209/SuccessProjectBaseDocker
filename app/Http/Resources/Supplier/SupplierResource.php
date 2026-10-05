<?php

namespace App\Http\Resources\Supplier;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'supplier_id'   => $this->supplier_id,
            'supplier_code' => $this->supplier_code,
            'sector_id'     => $this->sector_id,
            'sector_name'   => $this->sector?->name,
            'name'          => $this->name,
            'contact'       => $this->contact,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'rfc'           => $this->rfc,
            'postal_code'   => $this->postal_code,
            'state'         => $this->state,
            'city'          => $this->city,
            'district'      => $this->district,
            'address'       => $this->address,
            'country'       => $this->country,
            'created_at'    => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'    => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
