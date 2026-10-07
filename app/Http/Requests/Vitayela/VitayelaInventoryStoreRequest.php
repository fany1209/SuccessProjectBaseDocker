<?php

namespace App\Http\Requests\Vitayela;

use Illuminate\Foundation\Http\FormRequest;

class VitayelaInventoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('producto_descripcion')) {
            $merge['producto_descripcion'] = strip_tags(trim((string)$this->input('producto_descripcion')));
        }
        if ($this->has('unidad')) {
            $merge['unidad'] = $this->filled('unidad') ? strip_tags(trim((string)$this->input('unidad'))) : null;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'producto_descripcion' => ['required', 'string', 'max:255'],
            'cantidad' => ['nullable', 'numeric', 'min:0'],
            'unidad' => ['nullable', 'string', 'max:50'],
            'stock_min' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'producto_descripcion.required' => 'La descripción del producto es obligatoria.',
            'producto_descripcion.string' => 'La descripción del producto debe ser texto.',
            'producto_descripcion.max' => 'La descripción no debe exceder los 255 caracteres.',
            'cantidad.numeric' => 'La cantidad debe ser un número válido.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
            'unidad.max' => 'La unidad no debe exceder 50 caracteres.',
            'stock_min.numeric' => 'El stock mínimo debe ser un número válido.',
            'stock_min.min' => 'El stock mínimo no puede ser negativo.',
        ];
    }
}
