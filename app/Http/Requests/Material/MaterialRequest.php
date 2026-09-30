<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
        $fields = [
            'name',
            'um',
            'brand',
        ];

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
     * Reglas de validación para registro y actualización de materiales de laboratorio.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('POST');

        return [
            'name'     => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:255'],
            'um'       => ['nullable', 'string', 'max:50'],
            'brand'    => ['nullable', 'string', 'max:255'],
            'entries'  => ['nullable', 'numeric', 'min:0'],
            'exits'    => ['nullable', 'numeric', 'min:0'],
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
            'name.required'   => 'El nombre del material es obligatorio.',
            'name.string'     => 'El nombre debe ser una cadena de texto.',
            'name.max'        => 'El nombre no debe superar los 255 caracteres.',
            'um.string'       => 'La unidad de medida debe ser texto.',
            'um.max'          => 'La unidad de medida no debe superar los 50 caracteres.',
            'brand.string'    => 'La marca debe ser una cadena de texto.',
            'brand.max'       => 'La marca no debe superar los 255 caracteres.',
            'entries.numeric' => 'Las entradas deben ser un valor numérico.',
            'entries.min'     => 'Las entradas no pueden ser negativas.',
            'exits.numeric'   => 'Las salidas deben ser un valor numérico.',
            'exits.min'       => 'Las salidas no pueden ser negativas.',
        ];
    }
}
