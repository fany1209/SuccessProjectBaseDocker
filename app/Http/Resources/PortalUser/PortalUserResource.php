<?php

namespace App\Http\Resources\PortalUser;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'portal_id'       => $this->portal_id ?? $this->id,
            'id'              => $this->id ?? $this->portal_id,
            'customer_id'     => $this->customer_id,
            'nombre_contacto' => $this->nombre_contacto,
            'empresa'         => $this->empresa,
            'email'           => $this->email,
            'is_active'       => (int) $this->is_active,
            'customer_code'   => $this->customer_code ?? ($this->customer->customer_code ?? null),
            'customer_name'   => $this->customer_name ?? ($this->customer->name ?? null),
            'created_at'      => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at'      => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
