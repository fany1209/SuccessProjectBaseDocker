<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCliRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'location_id' => 'required|integer|min:1',
            'concept_id' => 'required|array',
            'concept_id.*' => 'required|integer|min:1',
            'inventory_id' => 'required|array',
            'inventory_id.*' => 'required|integer|min:1',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.001', // Cambiado de 1 a 0.001
            'weight_per_unit' => 'required|array',
            'weight_per_unit.*' => 'required|numeric|min:0.001', // Cambiado de 1 a 0.001
            'bag_number' => 'nullable|array',
            'bag_number.*' => 'nullable|array',
            'bag_number.*.*' => 'required|string',
        ];
    }
}
