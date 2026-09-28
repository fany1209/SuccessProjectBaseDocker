<?php

namespace App\Http\Resources\Input;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InputResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'input_id'             => (int) $this->input_id,
            'supplier_id'          => $this->supplier_id ? (int) $this->supplier_id : null,
            'supplier_name'        => $this->sName ?? ($this->supplier->name ?? null),
            'transport_line_id'    => $this->transport_line_id ? (int) $this->transport_line_id : null,
            'transport_line_name'  => $this->tName ?? ($this->transportLine->name ?? null),
            'operator'             => $this->operator,
            'license_number'       => $this->license_number,
            'security_seal'        => (int) ($this->security_seal ?? 0),
            'security_seal_number' => $this->security_seal_number,
            'unit_plates'          => $this->unit_plates,
            'trailer_plates'       => $this->trailer_plates,
            'comments'             => $this->comments,
            'created_at'           => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at'           => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
            'products'             => $this->whenLoaded('products'),
        ];
    }
}
