<?php

namespace App\Http\Requests\Logistic;

use Illuminate\Foundation\Http\FormRequest;

class LogisticRequest extends FormRequest
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
     * Reglas de validación para consultas logísticas.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'transport_line_id' => ['nullable', 'integer'],
            'from'              => ['nullable', 'date'],
            'to'                => ['nullable', 'date'],
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
            'transport_line_id.integer' => 'El identificador de línea de transporte debe ser un número entero.',
            'from.date'                 => 'La fecha inicial debe ser una fecha válida.',
            'to.date'                   => 'La fecha final debe ser una fecha válida.',
        ];
    }
}
