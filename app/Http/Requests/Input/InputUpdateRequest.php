<?php

namespace App\Http\Requests\Input;

use Illuminate\Foundation\Http\FormRequest;

class InputUpdateRequest extends FormRequest
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

        $quantities = $this->input('quantity', []);
        if (is_array($quantities)) {
            $quantities = array_map(function ($q) {
                if (is_string($q)) {
                    $q = trim(str_replace(',', '.', $q));
                }
                return is_numeric($q) ? (float) $q : $q;
            }, $quantities);

            $sanitized['quantity'] = $quantities;
        }

        $warehouseBatches = $this->input('warehouse_batch', []);
        if (is_array($warehouseBatches)) {
            $warehouseBatches = array_map(function ($b) {
                return is_string($b) ? strip_tags(trim($b)) : $b;
            }, $warehouseBatches);

            $sanitized['warehouse_batch'] = $warehouseBatches;
        }

        $sanitized['security_seal'] = $this->filled('security_seal_number') ? 1 : 0;

        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'id'                   => ['nullable', 'integer'],
            'operator'             => ['nullable', 'string', 'max:200'],
            'license_number'       => ['nullable', 'string', 'max:50'],
            'security_seal'        => ['nullable', 'integer', 'in:0,1'],
            'security_seal_number' => ['nullable', 'string', 'max:50'],
            'unit_plates'          => ['nullable', 'string', 'max:20'],
            'trailer_plates'       => ['nullable', 'string', 'max:20'],
            'comments'             => ['nullable', 'string', 'max:300'],
            'supplier_id'          => ['required', 'integer'],
            'transport_line_id'    => ['nullable', 'integer'],
            'warehouse_batch'      => ['required', 'array', 'min:1'],
            'warehouse_batch.*'    => ['required', 'string', 'max:50'],
            'quantity'             => ['required', 'array', 'min:1'],
            'quantity.*'           => ['required', 'numeric', 'min:0.001'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required'       => 'El proveedor es obligatorio.',
            'supplier_id.integer'        => 'El proveedor debe ser un identificador numérico.',
            'warehouse_batch.required'   => 'Debe ingresar al menos un lote de almacén.',
            'warehouse_batch.min'        => 'Debe ingresar al menos un lote de almacén.',
            'warehouse_batch.*.required' => 'El número de lote es obligatorio.',
            'quantity.required'          => 'Debe ingresar al menos una cantidad.',
            'quantity.min'               => 'Debe ingresar al menos una cantidad.',
            'quantity.*.required'        => 'La cantidad es obligatoria para cada producto.',
            'quantity.*.numeric'         => 'La cantidad debe ser un valor numérico.',
            'quantity.*.min'             => 'La cantidad mínima por producto debe ser mayor a 0.',
        ];
    }
}
