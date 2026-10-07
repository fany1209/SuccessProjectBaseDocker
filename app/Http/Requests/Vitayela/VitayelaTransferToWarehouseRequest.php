<?php

namespace App\Http\Requests\Vitayela;

use Illuminate\Foundation\Http\FormRequest;

class VitayelaTransferToWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('unit_type')) {
            $this->merge([
                'unit_type' => $this->filled('unit_type') ? strip_tags(trim((string)$this->input('unit_type'))) : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,product_id'],
            'cantidad_salida' => ['required', 'numeric', 'min:0.01'],
            'peso_por_unidad' => ['required', 'numeric', 'min:0.01'],
            'unit_type' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Debe seleccionar un producto.',
            'product_id.exists' => 'El producto seleccionado no existe en el catálogo.',
            'cantidad_salida.required' => 'La cantidad a enviar es obligatoria.',
            'cantidad_salida.numeric' => 'La cantidad a enviar debe ser un número válido.',
            'cantidad_salida.min' => 'La cantidad a enviar debe ser al menos 0.01.',
            'peso_por_unidad.required' => 'El peso por unidad es obligatorio.',
            'peso_por_unidad.numeric' => 'El peso por unidad debe ser numérico.',
            'peso_por_unidad.min' => 'El peso por unidad debe ser al menos 0.01.',
            'unit_type.max' => 'El tipo de unidad no debe exceder 50 caracteres.',
        ];
    }
}
