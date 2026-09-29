<?php

namespace App\Http\Requests\LabChart;

use Illuminate\Foundation\Http\FormRequest;

class LabChartRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva anti Stored-XSS antes de validar los parámetros.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = strip_tags(trim($value));
            }
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para consultas de gráficos de laboratorio.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['nullable', 'string', 'max:50'],
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date'],
        ];
    }

    /**
     * Mensajes descriptivos en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.string' => 'El tipo de gráfico debe ser una cadena de texto.',
            'type.max'    => 'El tipo de gráfico no debe exceder 50 caracteres.',
            'from.date'   => 'La fecha inicial debe ser una fecha válida.',
            'to.date'     => 'La fecha final debe ser una fecha válida.',
        ];
    }
}
