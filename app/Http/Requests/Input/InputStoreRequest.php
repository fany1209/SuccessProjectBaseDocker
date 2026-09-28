<?php

namespace App\Http\Requests\Input;

use App\Models\Operator;
use Illuminate\Foundation\Http\FormRequest;

class InputStoreRequest extends FormRequest
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
                $clean = strip_tags(trim($value));
                $sanitized[$key] = $clean === '' ? null : $clean;
            }
        }

        if ($this->filled('operator_id') && (!$this->filled('operator') || !$this->filled('license_number'))) {
            $op = Operator::find($this->input('operator_id'));
            if ($op) {
                $sanitized['operator'] = $sanitized['operator'] ?? $op->name;
                $sanitized['license_number'] = $sanitized['license_number'] ?? $op->license;
            }
        }

        if ($this->filled('security_seal_number') && !$this->filled('security_seal')) {
            $sanitized['security_seal'] = 1;
        }

        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'supplier_id'          => ['required', 'integer'],
            'transport_line_id'    => ['nullable', 'integer'],
            'operator_id'          => ['nullable', 'integer'],
            'operator'             => ['nullable', 'string', 'max:200'],
            'license_number'       => ['nullable', 'string', 'max:50'],
            'security_seal'        => ['nullable', 'integer', 'in:0,1'],
            'security_seal_number' => ['nullable', 'string', 'max:50'],
            'unit_plates'          => ['nullable', 'string', 'max:20'],
            'trailer_plates'       => ['nullable', 'string', 'max:20'],
            'comments'             => ['nullable', 'string', 'max:300'],
            'product_id'           => ['required', 'array', 'min:1'],
            'product_id.*'         => ['required', 'integer'],
            'stock'                => ['required', 'array', 'min:1'],
            'warehouse_batch'      => ['required', 'array', 'min:1'],
            'warehouse_batch.*'    => ['required', 'string', 'max:50'],
            'concept_id'           => ['nullable', 'array'],
            'platforms'            => ['nullable', 'array'],
            'weight_per_unit'      => ['nullable', 'array'],
            'quantity'             => ['nullable', 'array'],
            'location_name'        => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required'       => 'El proveedor es obligatorio.',
            'supplier_id.integer'        => 'El proveedor debe ser un identificador numérico.',
            'product_id.required'        => 'Debe incluir al menos un producto.',
            'product_id.min'             => 'Debe incluir al menos un producto.',
            'stock.required'             => 'Debe ingresar el stock inicial por producto.',
            'warehouse_batch.required'   => 'Debe ingresar el lote por cada producto.',
            'warehouse_batch.*.required' => 'El lote es obligatorio.',
        ];
    }
}
