<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class QualityUpdateInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        $stringFields = [
            'inspector',
            'responsable',
            'hora_turno',
            'area_otro',
            'comentarios_q',
        ];

        foreach ($stringFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $sanitized[$field] = strip_tags(trim($this->input($field)));
            }
        }

        if ($this->has('obs') && is_array($this->input('obs'))) {
            $obs = $this->input('obs');
            foreach ($obs as $i => $row) {
                if (is_array($row)) {
                    if (isset($row['name']) && is_string($row['name'])) {
                        $obs[$i]['name'] = strip_tags(trim($row['name']));
                    }
                    if (isset($row['ubicacion']) && is_string($row['ubicacion'])) {
                        $obs[$i]['ubicacion'] = strip_tags(trim($row['ubicacion']));
                    }
                }
            }
            $sanitized['obs'] = $obs;
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    public function rules(): array
    {
        return [
            'inspection_id'               => ['required', 'integer', 'exists:inspections_w,id'],
            'fecha_inspeccion'            => ['nullable', 'date'],
            'inspector'                   => ['nullable', 'string', 'max:255'],
            'hora_turno'                  => ['nullable', 'string', 'max:10'],
            'turno'                       => ['nullable', 'in:1,2,3,mixto'],
            'area'                        => ['nullable', 'array'],
            'area.*'                      => ['in:nave1,nave2,otro'],
            'area_otro'                   => ['nullable', 'string', 'max:255'],
            'responsable'                 => ['nullable', 'string', 'max:255'],
            'comentarios_q'               => ['nullable', 'string'],
            'obs'                         => ['nullable', 'array'],
            'obs.*.id'                    => ['nullable', 'integer', 'exists:observaciones,id'],
            'obs.*.name'                  => ['nullable', 'string', 'max:255'],
            'obs.*.rev'                   => ['nullable', 'in:cumple,no_cumple'],
            'obs.*.fecha'                 => ['nullable', 'date'],
            'obs.*.evidencia_file'        => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'obs.*.existing_evidencia_path' => ['nullable', 'string', 'max:500'],
            'obs.*.ubicacion'             => ['nullable', 'string', 'max:255'],
        ];
    }
}
