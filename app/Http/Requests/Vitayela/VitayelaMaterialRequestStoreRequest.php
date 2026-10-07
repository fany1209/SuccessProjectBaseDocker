<?php

namespace App\Http\Requests\Vitayela;

use Illuminate\Foundation\Http\FormRequest;

class VitayelaMaterialRequestStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('applicant_name')) {
            $merge['applicant_name'] = strip_tags(trim((string)$this->input('applicant_name')));
        }
        if ($this->has('comments')) {
            $merge['comments'] = $this->filled('comments') ? strip_tags(trim((string)$this->input('comments'))) : null;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'applicant_name' => ['required', 'string', 'max:100'],
            'comments' => ['nullable', 'string'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.name' => ['required', 'string', 'max:255'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_name.required' => 'El nombre del solicitante es obligatorio.',
            'applicant_name.max' => 'El nombre del solicitante no debe superar los 100 caracteres.',
            'products.required' => 'Debe incluir al menos un producto en la solicitud.',
            'products.array' => 'La lista de productos no es válida.',
            'products.min' => 'Debe solicitar al menos un producto.',
            'products.*.name.required' => 'El nombre del producto es obligatorio.',
            'products.*.quantity.required' => 'La cantidad requerida es obligatoria.',
            'products.*.quantity.min' => 'La cantidad requerida debe ser al menos 0.01.',
        ];
    }
}
