<?php

namespace App\Http\Resources\Cli;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CliResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cli_id'          => $this->cli_id,
            'location_id'     => $this->location_id,
            'concept_id'      => $this->concept_id,
            'inventory_id'    => $this->inventory_id,
            'quantity'        => (float) $this->quantity,
            'weight_per_unit' => (float) $this->weight_per_unit,
            'net_weight'      => (float) $this->net_weight,
            'bag_number'      => $this->bag_number,
            'protein'         => $this->protein !== null ? (float) $this->protein : null,
            'sq_certificate'  => $this->sq_certificate,
            'inventory'       => $this->whenLoaded('inventory'),
            'location'        => $this->whenLoaded('location'),
            'concept'         => $this->whenLoaded('concept'),
            'created_at'      => $this->created_at?->format('d-m-Y H:i:s'),
            'updated_at'      => $this->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
