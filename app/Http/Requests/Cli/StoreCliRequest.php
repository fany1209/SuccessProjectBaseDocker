<?php

namespace App\Http\Requests\Cli;

use Illuminate\Foundation\Http\FormRequest;

class StoreCliRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_id'       => 'required|integer|min:1',
            'concept_id'        => 'required|array',
            'concept_id.*'      => 'required|integer|min:1',
            'inventory_id'      => 'required|array',
            'inventory_id.*'    => 'required|integer|min:1',
            'quantity'          => 'required|array',
            'quantity.*'        => 'required|numeric|min:0.001',
            'weight_per_unit'   => 'required|array',
            'weight_per_unit.*' => 'required|numeric|min:0.001',
            'bag_number'        => 'nullable|array',
            'bag_number.*'      => 'nullable|array',
            'bag_number.*.*'    => 'required|string|max:50',
            'protein'           => 'nullable|array',
            'protein.*'         => 'nullable|array',
            'protein.*.*'       => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required'     => 'La ubicación en almacén es obligatoria.',
            'concept_id.required'      => 'Debe especificar al menos un concepto.',
            'inventory_id.required'    => 'Debe seleccionar al menos un producto del inventario.',
            'quantity.required'        => 'La cantidad es obligatoria.',
            'weight_per_unit.required' => 'El peso unitario es obligatorio.',
        ];
    }
}
