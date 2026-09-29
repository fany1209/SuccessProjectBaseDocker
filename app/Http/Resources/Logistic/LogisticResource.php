<?php

namespace App\Http\Resources\Logistic;

use Illuminate\Http\Resources\Json\JsonResource;

class LogisticResource extends JsonResource
{
    /**
     * Transforma el recurso logístico en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'count_operators' => (int) ($this['count_operators'] ?? 0),
            'count_tLines'    => (int) ($this['count_tLines'] ?? 0),
            'count_vehicles'  => (int) ($this['count_vehicles'] ?? 0),
            'count_trailers'  => (int) ($this['count_trailers'] ?? 0),
            'vehicles_per_tl' => $this['vehicles_per_tl'] ?? [],
            'trailers_per_tl' => $this['trailers_per_tl'] ?? [],
        ];
    }
}
