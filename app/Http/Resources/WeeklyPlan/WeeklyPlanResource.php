<?php

namespace App\Http\Resources\WeeklyPlan;

use Illuminate\Http\Resources\Json\JsonResource;

class WeeklyPlanResource extends JsonResource
{
    /**
     * Transforma el recurso de plan semanal de trabajo en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'               => (int) $this->id,
            'report_code'      => $this->week_range ?: sprintf('WKP-%05d', $this->id),
            'week_range'       => $this->week_range,
            'entry_date'       => $this->review_date ? (is_string($this->review_date) ? $this->review_date : $this->review_date->format('Y-m-d')) : '—',
            'issue_date'       => optional($this->created_at)->format('Y-m-d'),
            'client_name'      => $this->responsible_name ?: ($this->project_name ?: '—'),
            'project_name'     => $this->project_name,
            'responsible_name' => $this->responsible_name,
            'total_hours'      => $this->total_hours,
            'pdf_url'          => route('weekly.plans.pdf', $this->id),
            'delete_url'       => route('weekly.plans.delete', $this->id),
            'created_at'       => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'       => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
