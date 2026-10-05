<?php

namespace App\Http\Requests\SupplierPrice;

use Illuminate\Foundation\Http\FormRequest;

class SupplierPriceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = strip_tags(trim($value));
            }
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'insumo'           => 'required|string|max:255',
            'clave_sat'        => 'nullable|string|max:20',
            'proveedor'        => 'required|string|max:255',
            'precio'           => 'required|numeric|min:0',
            'tiene_iva'        => 'nullable',
            'moneda'           => 'nullable|string|max:3',
            'fecha_cotizacion' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'insumo.required'           => 'El nombre del insumo es obligatorio.',
            'insumo.max'                => 'El insumo no puede exceder 255 caracteres.',
            'clave_sat.max'             => 'La clave SAT no puede exceder 20 caracteres.',
            'proveedor.required'        => 'El nombre del proveedor es obligatorio.',
            'proveedor.max'             => 'El proveedor no puede exceder 255 caracteres.',
            'precio.required'           => 'El precio es obligatorio.',
            'precio.numeric'            => 'El precio debe ser un número válido.',
            'precio.min'                => 'El precio no puede ser negativo.',
            'moneda.max'                => 'La moneda no puede exceder 3 caracteres.',
            'fecha_cotizacion.required' => 'La fecha de cotización es obligatoria.',
            'fecha_cotizacion.date'     => 'La fecha de cotización debe ser una fecha válida.',
        ];
    }
}
