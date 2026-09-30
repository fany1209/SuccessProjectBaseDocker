<?php

namespace App\Http\Requests\Operator;

use Illuminate\Foundation\Http\FormRequest;

class OperatorRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva anti Stored-XSS antes de validar los datos.
     */
    protected function prepareForValidation(): void
    {
        $fields = ['name', 'license'];
        $sanitized = [];

        foreach ($fields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $sanitized[$field] = strip_tags(trim($this->input($field)));
            }
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para registro y actualización de operadores.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('POST');

        return [
            'name'    => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:100'],
            'license' => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:20'],
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
            'name.required'    => 'El nombre del operador es obligatorio.',
            'name.string'      => 'El nombre debe ser una cadena de texto.',
            'name.max'         => 'El nombre no debe superar los 100 caracteres.',
            'license.required' => 'La licencia del operador es obligatoria.',
            'license.string'   => 'La licencia debe ser una cadena de texto.',
            'license.max'      => 'La licencia no debe superar los 20 caracteres.',
        ];
    }
}
