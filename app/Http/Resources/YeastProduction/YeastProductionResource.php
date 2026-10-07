<?php

namespace App\Http\Resources\YeastProduction;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YeastProductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'yeast_production_id' => $this->yeast_production_id,
            'output_id' => $this->output_id,
            'date' => $this->date,
            'bag_number' => $this->bag_number,
            'internal_weight' => $this->internal_weight !== null ? (float)$this->internal_weight : null,
            'external_weight' => $this->external_weight !== null ? (float)$this->external_weight : null,
            'bags_natural' => $this->bags_natural !== null ? (int)$this->bags_natural : null,
            'bags_mix' => $this->bags_mix !== null ? (int)$this->bags_mix : null,
            'bags_white' => $this->bags_white !== null ? (int)$this->bags_white : null,
            'finished_product_kg' => $this->finished_product_kg !== null ? (float)$this->finished_product_kg : null,
            'bags_quantity' => $this->bags_quantity !== null ? (int)$this->bags_quantity : null,
            'output' => $this->whenLoaded('output'),
            'pallets' => $this->whenLoaded('pallets'),
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
