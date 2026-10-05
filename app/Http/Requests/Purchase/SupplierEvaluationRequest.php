<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class SupplierEvaluationRequest extends FormRequest
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
            'supplier_name'   => 'required|string|max:200',
            'rfc'             => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:255',
            'evaluation_date' => 'nullable|date',
            'evaluator'       => 'nullable|string|max:150',
            'products'        => 'nullable|string|max:255',
            'observations'    => 'nullable|string',
            'answers'         => 'nullable|array',
            'qualification'   => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_name.required' => 'El nombre del proveedor es obligatorio.',
            'supplier_name.max'      => 'El nombre del proveedor no puede exceder 200 caracteres.',
        ];
    }
}
