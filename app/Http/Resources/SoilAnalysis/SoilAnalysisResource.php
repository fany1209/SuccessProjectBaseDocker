<?php

namespace App\Http\Resources\SoilAnalysis;

use Illuminate\Http\Resources\Json\JsonResource;

class SoilAnalysisResource extends JsonResource
{
    /**
     * Transforma el recurso de análisis de suelo en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => (int) $this->id,
            'report_code' => $this->report_code ?? '—',
            'entry_date'  => $this->entry_date ? (is_string($this->entry_date) ? $this->entry_date : $this->entry_date->format('Y-m-d')) : '—',
            'issue_date'  => $this->issue_date ? (is_string($this->issue_date) ? $this->issue_date : $this->issue_date->format('Y-m-d')) : '—',
            'client_name' => $this->client_name ?? '—',
            'pdf_url'     => route('soil.analyses.pdf', ['id' => $this->id]),
            'delete_url'  => route('soil.analyses.delete', ['id' => $this->id]),
            'created_at'  => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'  => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
