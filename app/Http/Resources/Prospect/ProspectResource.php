<?php

namespace App\Http\Resources\Prospect;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProspectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'prospect_id'  => $this->prospect_id,
            'sector_id'    => $this->sector_id,
            'sector'       => $this->sector?->name ?? null,
            'name'         => $this->name,
            'phone'        => $this->phone,
            'email'        => $this->email,
            'rfc'          => $this->rfc,
            'state'        => $this->state,
            'city'         => $this->city,
            'district'     => $this->district,
            'address'      => $this->address,
            'full_address' => trim(implode(', ', array_filter([$this->address, $this->district, $this->city, $this->state]))),
            'created_at'   => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at'   => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
