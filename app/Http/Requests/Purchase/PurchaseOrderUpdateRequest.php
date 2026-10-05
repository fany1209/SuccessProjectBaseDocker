<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderUpdateRequest extends FormRequest
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
            'id'               => 'required|string|max:50',
            'supplier_id'      => 'required|integer|exists:suppliers,supplier_id',
            'contact'          => 'nullable|string|max:150',
            'delivery_time'    => 'nullable|string|max:100',
            'delivery_date'    => 'nullable|date',
            'guia'             => 'nullable|string|max:100',
            'cfdi'             => 'nullable|string|max:10',
            'payment_method'   => 'nullable|string|max:10',
            'method_payment'   => 'nullable|string|max:10',
            'application_date' => 'nullable|date',
            'applicant'        => 'nullable|string|max:150',
            'price'            => 'nullable|numeric',
            'product_name'     => 'nullable|array',
            'product_name.*'   => 'nullable|string|max:255',
            'quantity'         => 'nullable|array',
            'quantity.*'       => 'nullable|numeric',
            'iva'              => 'nullable|array',
            'unit_price'       => 'nullable|array',
            'unit_price.*'     => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'          => 'El folio de la orden es obligatorio.',
            'supplier_id.required' => 'El proveedor es obligatorio.',
            'supplier_id.exists'   => 'El proveedor seleccionado no existe.',
        ];
    }
}
