<?php

namespace App\Http\Requests\Laboratory;

use Illuminate\Foundation\Http\FormRequest;

class LaboratorySampleInvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        $stringFields = [
            'folio_muestra',
            'tipo_muestra',
            'proveedor',
            'sku',
            'producto',
            'presentacion',
            'ubicacion_stock',
            'motivo_salida',
            'solicitante',
            'recolector',
            'cliente',
            'status',
        ];

        foreach ($stringFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $sanitized[$field] = strip_tags(trim($this->input($field)));
            }
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    public function rules(): array
    {
        return [
            'folio_muestra'   => ['nullable', 'string', 'max:50'],
            'tipo_muestra'    => ['required', 'string', 'max:150'],
            'proveedor'       => ['nullable', 'string', 'max:255'],
            'sku'             => ['nullable', 'string', 'max:100'],
            'producto'        => ['required', 'string', 'max:255'],
            'stock_inicial'   => ['nullable', 'numeric', 'min:0'],
            'presentacion'    => ['nullable', 'string', 'max:100'],
            'ubicacion_stock' => ['nullable', 'string', 'max:255'],
            'fecha_entrada'   => ['nullable', 'date'],
            'fecha_salida'    => ['nullable', 'date'],
            'cantidad_salida' => ['nullable', 'numeric', 'min:0'],
            'motivo_salida'   => ['nullable', 'string', 'max:255'],
            'solicitante'     => ['nullable', 'string', 'max:255'],
            'recolector'      => ['nullable', 'string', 'max:255'],
            'cliente'         => ['nullable', 'string', 'max:255'],
            'status'          => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_muestra.required' => 'El tipo de muestra es obligatorio.',
            'producto.required'     => 'El producto es obligatorio.',
        ];
    }
}
