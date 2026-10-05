<?php

namespace App\Http\Requests\SupplierCertificate;

use Illuminate\Foundation\Http\FormRequest;

class SupplierCertificateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];
        foreach ($this->except(['file']) as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = strip_tags(trim($value));
            }
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'supplier_id'   => 'required|exists:suppliers,supplier_id',
            'product_id'    => 'required|exists:products,product_id',
            'fecha_emision' => 'required|date',
            'file'          => 'required|mimes:pdf|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required'   => 'El proveedor es obligatorio.',
            'supplier_id.exists'     => 'El proveedor seleccionado no existe.',
            'product_id.required'    => 'El producto es obligatorio.',
            'product_id.exists'      => 'El producto seleccionado no existe.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date'     => 'La fecha de emisión debe ser una fecha válida.',
            'file.required'          => 'El archivo de certificado PDF es obligatorio.',
            'file.mimes'             => 'El archivo debe estar en formato PDF.',
            'file.max'               => 'El archivo PDF no puede exceder 2 MB (2048 KB).',
        ];
    }
}
