<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class SupplierSelectionCriteriaRequest extends FormRequest
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
            'supplier' => 'required|string|max:200',
            'address'  => 'nullable|string|max:255',
            'date'     => 'nullable|date',
            'products' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier.required' => 'El nombre del proveedor es obligatorio.',
            'supplier.max'      => 'El nombre del proveedor no puede exceder 200 caracteres.',
        ];
    }
}
