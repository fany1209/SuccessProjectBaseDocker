<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;

class ProductionInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'area'                 => is_string($this->area) ? strtolower(strip_tags(trim($this->area))) : $this->area,
            'producto_descripcion' => is_string($this->producto_descripcion) ? strip_tags(trim($this->producto_descripcion)) : $this->producto_descripcion,
            'unidad'               => is_string($this->unidad) ? strip_tags(trim($this->unidad)) : $this->unidad,
        ]);
    }

    public function rules(): array
    {
        return [
            'area'                 => ['required', 'string', 'in:fertil,vitayela'],
            'producto_descripcion' => ['required', 'string', 'max:255'],
            'cantidad'             => ['nullable', 'numeric', 'min:0'],
            'unidad'               => ['nullable', 'string', 'max:50'],
            'stock_min'            => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'area.required'                 => 'El área del inventario (Fertil o Vitayela) es obligatoria.',
            'area.in'                       => 'El área seleccionada debe ser Fertil o Vitayela.',
            'producto_descripcion.required' => 'La descripción o producto es obligatoria.',
            'producto_descripcion.max'      => 'La descripción o producto no puede exceder los 255 caracteres.',
            'cantidad.numeric'              => 'La cantidad debe ser un valor numérico.',
            'cantidad.min'                  => 'La cantidad no puede ser negativa.',
            'unidad.max'                    => 'La unidad no puede exceder los 50 caracteres.',
            'stock_min.numeric'             => 'El stock mínimo debe ser un valor numérico.',
            'stock_min.min'                 => 'El stock mínimo no puede ser negativo.',
        ];
    }
}

