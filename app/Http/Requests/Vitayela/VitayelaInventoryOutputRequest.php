<?php

namespace App\Http\Requests\Vitayela;

use Illuminate\Foundation\Http\FormRequest;

class VitayelaInventoryOutputRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cantidad_salida' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad_salida.required' => 'La cantidad de salida es obligatoria.',
            'cantidad_salida.numeric' => 'La cantidad de salida debe ser un valor numérico.',
            'cantidad_salida.min' => 'La cantidad de salida debe ser de al menos 0.01.',
        ];
    }
}
