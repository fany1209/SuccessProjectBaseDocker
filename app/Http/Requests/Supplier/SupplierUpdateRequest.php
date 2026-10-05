<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class SupplierUpdateRequest extends FormRequest
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
        $supplierId = $this->input('supplier_id') ?? $this->route('supplier') ?? $this->route('id');

        return [
            'supplier_id' => 'sometimes|required|integer|exists:suppliers,supplier_id',
            'name'        => 'sometimes|required|string|max:200',
            'sector_id'   => 'nullable|integer|exists:sectors,sector_id',
            'contact'     => 'nullable|string|max:200',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:150',
            'rfc'         => 'nullable|string|max:15|unique:suppliers,rfc,' . $supplierId . ',supplier_id',
            'postal_code' => 'nullable|string|max:10',
            'state'       => 'nullable|string|max:80',
            'city'        => 'nullable|string|max:80',
            'district'    => 'nullable|string|max:80',
            'address'     => 'nullable|string|max:200',
            'country'     => 'nullable|string|max:80',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'El identificador del proveedor es obligatorio.',
            'supplier_id.exists'   => 'El proveedor especificado no existe.',
            'name.required'        => 'El nombre del proveedor es obligatorio.',
            'name.max'             => 'El nombre del proveedor no puede exceder 200 caracteres.',
            'sector_id.exists'     => 'El sector seleccionado no es válido.',
            'email.email'          => 'El formato del correo electrónico es inválido.',
            'email.max'            => 'El correo electrónico no puede exceder 150 caracteres.',
            'phone.max'            => 'El teléfono no puede exceder 20 caracteres.',
            'rfc.max'              => 'El RFC no puede exceder 15 caracteres.',
            'rfc.unique'           => 'El RFC ya ha sido registrado previamente.',
        ];
    }
}
