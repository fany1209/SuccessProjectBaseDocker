<?php

namespace App\Http\Requests\Requisition;

use Illuminate\Foundation\Http\FormRequest;

class RequisitionCheckRequest extends FormRequest
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
            'id'             => 'required|integer|exists:purchases_requisitions,id',
            'consecutive'    => 'required|string|max:30',
            'purchase_order' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'             => 'El identificador de la requisición es obligatorio.',
            'id.exists'               => 'La requisición especificada no existe.',
            'consecutive.required'    => 'El folio consecutivo es obligatorio.',
            'purchase_order.required' => 'El número de orden de compra es obligatorio.',
        ];
    }
}
