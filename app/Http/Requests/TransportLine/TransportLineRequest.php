<?php

namespace App\Http\Requests\TransportLine;

use Illuminate\Foundation\Http\FormRequest;

class TransportLineRequest extends FormRequest
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
        if ($this->has('name') && is_string($this->input('name'))) {
            $this->merge([
                'name' => strip_tags(trim($this->input('name'))),
            ]);
        }
    }

    /**
     * Reglas de validación para registro y actualización de líneas de transporte.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('POST');

        return [
            'name' => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:150'],
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
            'name.required' => 'El nombre de la línea de transporte es obligatorio.',
            'name.string'   => 'El nombre debe ser una cadena de texto.',
            'name.max'      => 'El nombre no debe superar los 150 caracteres.',
        ];
    }
}
