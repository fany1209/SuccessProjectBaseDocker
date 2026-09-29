<?php

namespace App\Http\Resources\LabChart;

use Illuminate\Http\Resources\Json\JsonResource;

class LabChartResource extends JsonResource
{
    /**
     * Transforma el recurso de conteos de laboratorio en un arreglo tipado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, int>
     */
    public function toArray($request): array
    {
        return [
            'reception_of_samples' => (int) ($this['reception_of_samples'] ?? $this->resource['reception_of_samples'] ?? 0),
            'weekly_results'       => (int) ($this['weekly_results'] ?? $this->resource['weekly_results'] ?? 0),
            'laboratory_samples'   => (int) ($this['laboratory_samples'] ?? $this->resource['laboratory_samples'] ?? 0),
            'pdf_clicks_d'         => (int) ($this['pdf_clicks_d'] ?? $this->resource['pdf_clicks_d'] ?? 0),
        ];
    }
}
