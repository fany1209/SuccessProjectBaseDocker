<?php

namespace App\Http\Requests\LotRequest;

use Illuminate\Foundation\Http\FormRequest;

class LotRequestRequest extends FormRequest
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
            'department',
            'comments',
            'product',
            'quantity',
            'provider',
            'collector',
            'sector',
            'sku',
            'batch',
            'status',
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
     * Reglas de validación para peticiones de lote.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('POST');

        return [
            'department' => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:100'],
            'comments'   => ['nullable', 'string', 'max:1000'],
            'product'    => ['nullable', 'string', 'max:255'],
            'quantity'   => ['nullable', 'string', 'max:255'],
            'provider'   => ['nullable', 'string', 'max:255'],
            'collector'  => ['nullable', 'string', 'max:255'],
            'sector'     => ['nullable', 'string', 'max:255'],
            'sku'        => ['nullable', 'string', 'max:255'],
            'batch'      => ['nullable', 'string', 'max:255'],
            'status'     => ['nullable', 'string', 'in:pendiente,terminado'],
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
            'department.required' => 'El departamento solicitante es obligatorio.',
            'department.string'   => 'El departamento debe ser una cadena de texto.',
            'department.max'      => 'El departamento no debe superar los 100 caracteres.',
            'comments.string'     => 'Los comentarios deben ser texto.',
            'comments.max'        => 'Los comentarios no deben superar los 1000 caracteres.',
            'product.string'      => 'El producto debe ser una cadena de texto.',
            'product.max'         => 'El producto no debe superar los 255 caracteres.',
            'quantity.string'     => 'La cantidad debe ser una cadena de texto.',
            'quantity.max'        => 'La cantidad no debe superar los 255 caracteres.',
            'provider.string'     => 'El proveedor debe ser una cadena de texto.',
            'provider.max'        => 'El proveedor no debe superar los 255 caracteres.',
            'collector.string'    => 'El recolector debe ser una cadena de texto.',
            'collector.max'       => 'El recolector no debe superar los 255 caracteres.',
            'sector.string'       => 'El sector debe ser una cadena de texto.',
            'sector.max'          => 'El sector no debe superar los 255 caracteres.',
            'sku.string'          => 'El SKU debe ser una cadena de texto.',
            'sku.max'             => 'El SKU no debe superar los 255 caracteres.',
            'batch.string'        => 'El lote debe ser una cadena de texto.',
            'batch.max'           => 'El lote no debe superar los 255 caracteres.',
            'status.in'           => 'El estatus debe ser pendiente o terminado.',
        ];
    }
}
