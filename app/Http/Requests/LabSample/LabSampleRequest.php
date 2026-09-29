<?php

namespace App\Http\Requests\LabSample;

use Illuminate\Foundation\Http\FormRequest;

class LabSampleRequest extends FormRequest
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
            'tipo_muestra',
            'producto',
            'sku',
            'proveedor',
            'ubicacion_stock',
            'status',
            'motivo_salida',
            'solicitante',
            'recolector',
            'cliente',
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
     * Reglas de validación para muestras de laboratorio.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tipo_muestra'     => ['nullable', 'string', 'max:150'],
            'producto'         => ['nullable', 'string', 'max:255'],
            'sku'              => ['nullable', 'string', 'max:100'],
            'proveedor'        => ['nullable', 'string', 'max:255'],
            'ubicacion_stock'  => ['nullable', 'string', 'max:255'],
            'fecha_entrada'    => ['nullable', 'date'],
            'fecha_salida'     => ['nullable', 'date'],
            'stock_inicial'    => ['nullable', 'numeric', 'min:0'],
            'cantidad_salida'  => ['nullable', 'numeric', 'min:0'],
            'status'           => ['nullable', 'string', 'max:50'],
            'motivo_salida'    => ['nullable', 'string', 'max:255'],
            'solicitante'      => ['nullable', 'string', 'max:255'],
            'recolector'       => ['nullable', 'string', 'max:255'],
            'cliente'          => ['nullable', 'string', 'max:255'],
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
            'tipo_muestra.string'    => 'El tipo de muestra debe ser una cadena de texto.',
            'tipo_muestra.max'       => 'El tipo de muestra no debe superar los 150 caracteres.',
            'producto.string'        => 'El producto debe ser una cadena de texto.',
            'producto.max'           => 'El producto no debe superar los 255 caracteres.',
            'sku.string'             => 'El SKU debe ser una cadena de texto.',
            'sku.max'                => 'El SKU no debe superar los 100 caracteres.',
            'proveedor.string'       => 'El proveedor debe ser una cadena de texto.',
            'proveedor.max'          => 'El proveedor no debe superar los 255 caracteres.',
            'ubicacion_stock.string' => 'La ubicación debe ser una cadena de texto.',
            'ubicacion_stock.max'    => 'La ubicación no debe superar los 255 caracteres.',
            'fecha_entrada.date'     => 'La fecha de entrada debe ser una fecha válida.',
            'fecha_salida.date'      => 'La fecha de salida debe ser una fecha válida.',
            'stock_inicial.numeric'  => 'El stock inicial debe ser numérico.',
            'stock_inicial.min'      => 'El stock inicial no puede ser negativo.',
            'cantidad_salida.numeric' => 'La cantidad de salida debe ser numérica.',
            'cantidad_salida.min'    => 'La cantidad de salida no puede ser negativa.',
            'status.string'          => 'El estatus debe ser una cadena de texto.',
            'status.max'             => 'El estatus no debe superar los 50 caracteres.',
        ];
    }
}
