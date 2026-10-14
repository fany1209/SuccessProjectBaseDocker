<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBigBagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        if ($this->has('weight')) {
            $rawWeight = $this->input('weight');
            $sanitized['weight'] = is_string($rawWeight) ? str_replace(',', '.', trim($rawWeight)) : $rawWeight;
        }

        if ($this->has('protein')) {
            $rawProtein = $this->input('protein');
            $sanitized['protein'] = is_string($rawProtein) && trim($rawProtein) !== '' 
                ? str_replace(',', '.', trim($rawProtein)) 
                : (is_numeric($rawProtein) ? $rawProtein : null);
        }

        if ($this->has('bag_number')) {
            $rawBag = $this->input('bag_number');
            $sanitized['bag_number'] = is_string($rawBag) ? strip_tags(trim($rawBag)) : $rawBag;
        }

        if ($this->has('packing_list')) {
            $rawPl = $this->input('packing_list');
            $sanitized['packing_list'] = is_string($rawPl) ? strip_tags(trim($rawPl)) : $rawPl;
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    public function rules(): array
    {
        return [
            'weight'       => 'required|numeric|min:0.01',
            'bag_number'   => 'nullable|string|max:100',
            'protein'      => 'nullable|numeric|min:0|max:100',
            'packing_list' => 'nullable|string|max:100',
            'location_id'  => 'nullable|integer|exists:locations,location_id',
        ];
    }

    public function messages(): array
    {
        return [
            'weight.required'    => 'El peso de la barcina es obligatorio.',
            'weight.numeric'     => 'El peso debe ser un valor numérico válido.',
            'weight.min'         => 'El peso debe ser mayor a 0 kg.',
            'protein.numeric'    => 'El porcentaje de proteína debe ser numérico.',
            'protein.min'        => 'La proteína no puede ser menor a 0%.',
            'protein.max'        => 'La proteína no puede ser mayor a 100%.',
            'location_id.exists' => 'La ubicación seleccionada no es válida.',
        ];
    }
}
