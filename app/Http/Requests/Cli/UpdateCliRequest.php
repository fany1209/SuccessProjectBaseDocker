<?php

namespace App\Http\Requests\Cli;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCliRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cli_id'          => 'nullable|integer',
            'location_id'     => 'required|integer|min:1',
            'concept_id'      => 'required|integer|min:1',
            'inventory_id'    => 'required|integer|min:1',
            'quantity'        => 'required|numeric|min:0.001',
            'weight_per_unit' => 'required|numeric|min:0.001',
            'bag_number'      => 'nullable|string|max:50',
            'protein'         => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required'     => 'La ubicación en almacén es obligatoria.',
            'concept_id.required'      => 'El concepto es obligatorio.',
            'inventory_id.required'    => 'El inventario es obligatorio.',
            'quantity.required'        => 'La cantidad es obligatoria.',
            'weight_per_unit.required' => 'El peso unitario es obligatorio.',
        ];
    }
}
