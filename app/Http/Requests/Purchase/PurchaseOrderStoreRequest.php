<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderStoreRequest extends FormRequest
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
            'supplier_id'      => 'nullable|integer|exists:suppliers,supplier_id',
            'name'             => 'required_without:supplier_id|nullable|string|max:200',
            'phone'            => 'nullable|string|max:20',
            'rfc'              => 'nullable|string|max:15',
            'address'          => 'nullable|string|max:200',
            'city'             => 'nullable|string|max:80',
            'state'            => 'nullable|string|max:80',
            'district'         => 'nullable|string|max:80',
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
            'id.required'                   => 'El folio o número de orden es obligatorio.',
            'supplier_id.exists'            => 'El proveedor seleccionado no existe.',
            'name.required_without'         => 'El nombre del proveedor es obligatorio cuando no se selecciona uno existente.',
            'delivery_date.date'            => 'La fecha de entrega debe ser válida.',
            'application_date.date'         => 'La fecha de aplicación debe ser válida.',
        ];
    }
}
