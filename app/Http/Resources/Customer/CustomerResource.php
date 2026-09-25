<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'customer_id' => $this->customer_id,
            'customer_code' => $this->customer_code,
            'sector_id' => $this->sector_id,
            'sector_name' => $this->sector?->name ?? $this->sector ?? null,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'rfc' => $this->rfc,
            'postal_code' => $this->postal_code,
            'state' => $this->state,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'country' => $this->country,
            'vendedor' => $this->vendedor,
            'contact' => $this->contact,
            'delivery_address' => $this->delivery_address,
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
