<?php

namespace App\Http\Requests\ItEquipment;

use Illuminate\Foundation\Http\FormRequest;

class ItEquipmentRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva anti Stored-XSS antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $fields = [
            'department',
            'responsible',
            'article',
            'brand',
            'model',
            'serial_number',
            'success_code',
            'image_url',
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
     * Reglas de validación para registro y edición de equipo de TI.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('POST');

        return [
            'department'    => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:255'],
            'responsible'   => ['nullable', 'string', 'max:255'],
            'article'       => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:255'],
            'brand'         => ['nullable', 'string', 'max:255'],
            'model'         => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'success_code'  => ['nullable', 'string', 'max:255'],
            'image_url'     => ['nullable', 'url', 'max:2048'],
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
            'department.required' => 'El departamento es obligatorio.',
            'department.string'   => 'El departamento debe ser una cadena de texto.',
            'department.max'      => 'El departamento no debe superar los 255 caracteres.',
            'responsible.string'  => 'El responsable debe ser una cadena de texto.',
            'responsible.max'     => 'El responsable no debe superar los 255 caracteres.',
            'article.required'    => 'El artículo es obligatorio.',
            'article.string'      => 'El artículo debe ser una cadena de texto.',
            'article.max'         => 'El artículo no debe superar los 255 caracteres.',
            'brand.string'        => 'La marca debe ser una cadena de texto.',
            'brand.max'           => 'La marca no debe superar los 255 caracteres.',
            'model.string'        => 'El modelo debe ser una cadena de texto.',
            'model.max'           => 'El modelo no debe superar los 255 caracteres.',
            'serial_number.string' => 'El número de serie debe ser una cadena de texto.',
            'serial_number.max'    => 'El número de serie no debe superar los 255 caracteres.',
            'success_code.string'  => 'El código success debe ser una cadena de texto.',
            'success_code.max'     => 'El código success no debe superar los 255 caracteres.',
            'image_url.url'       => 'La URL de la imagen debe ser una dirección URL válida.',
            'image_url.max'       => 'La URL de la imagen no debe superar los 2048 caracteres.',
        ];
    }
}
