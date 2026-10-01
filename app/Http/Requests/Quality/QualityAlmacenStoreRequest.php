<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class QualityAlmacenStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $mapTurno = [
            '1' => '1', '2' => '2', '3' => '3', 'mixto' => 'mixto',
            'matutina' => '1', 'mañana' => '1', 'am' => '1',
            'vespertina' => '2', 'tarde' => '2', 'pm' => '2',
            'nocturna' => '3', 'noche' => '3',
        ];

        $rawTurno = strtolower(trim((string) $this->input('turno')));
        $turnoNorm = $mapTurno[$rawTurno] ?? ($rawTurno === '' ? null : $rawTurno);

        $sanitized = [
            'turno' => $turnoNorm,
        ];

        $stringFields = [
            'inspector',
            'responsable',
            'area_otro',
            'comentarios',
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

        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'fecha_inspeccion'     => ['nullable', 'date'],
            'inspector'            => ['nullable', 'string', 'max:255'],
            'hora_turno'           => ['nullable', 'date_format:H:i'],
            'turno'                => ['nullable', 'in:1,2,3,mixto'],
            'area'                 => ['nullable', 'array'],
            'area.*'               => ['in:nave1,nave2,otro'],
            'area_otro'            => ['nullable', 'string', 'max:255'],
            'responsable'          => ['nullable', 'string', 'max:255'],
            'comentarios'          => ['nullable', 'string'],
            'comentarios_q'        => ['nullable', 'string'],
            'obs'                  => ['nullable', 'array'],
            'obs.*.name'           => ['nullable', 'string', 'max:255'],
            'obs.*.rev'            => ['nullable', 'in:cumple,no_cumple'],
            'obs.*.fecha'          => ['nullable', 'date'],
            'obs.*.evidencia_file' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'obs.*.ubicacion'      => ['nullable', 'string', 'max:255'],
        ];
    }
}
