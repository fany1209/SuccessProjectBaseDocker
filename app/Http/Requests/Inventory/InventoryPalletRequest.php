<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class InventoryPalletRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva anti Stored-XSS.
     */
    protected function prepareForValidation(): void
    {
        $stringFields = [
            'warehouse_batch',
            'operator',
            'license_number',
            'security_seal_number',
            'unit_plates',
            'trailer_plates',
            'comments',
        ];

        $sanitized = [];
        foreach ($stringFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $sanitized[$field] = strip_tags(trim($this->input($field)));
            }
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para aceptar e ingresar tarimas al inventario.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'location_id'          => ['required', 'exists:locations,location_id'],
            'supplier_id'          => ['nullable', 'exists:suppliers,supplier_id'],
            'concept_id'           => ['nullable', 'exists:concepts,concept_id'],
            'product_id'           => ['nullable', 'exists:products,product_id'],
            'quantity'             => ['nullable', 'numeric', 'min:0.01'],
            'weight_per_unit'      => ['nullable', 'numeric', 'min:0.01'],
            'final_weight'         => ['nullable', 'numeric', 'min:0.01'],
            'warehouse_batch'      => ['nullable', 'string', 'max:50'],
            'transport_line'       => ['nullable', 'integer'],
            'operator'             => ['nullable', 'string', 'max:200'],
            'license_number'       => ['nullable', 'string', 'max:50'],
            'security_seal'        => ['nullable', 'integer'],
            'security_seal_number' => ['nullable', 'string', 'max:50'],
            'unit_plates'          => ['nullable', 'string', 'max:20'],
            'trailer_plates'       => ['nullable', 'string', 'max:20'],
            'comments'             => ['nullable', 'string', 'max:300'],
        ];
    }

    /**
     * Mensajes claros en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'location_id.required'  => 'La ubicación en el almacén es obligatoria.',
            'location_id.exists'    => 'La ubicación seleccionada no existe.',
            'supplier_id.exists'    => 'El proveedor seleccionado no existe.',
            'concept_id.exists'     => 'El concepto seleccionado no existe.',
            'product_id.exists'     => 'El producto seleccionado no existe.',
            'quantity.numeric'      => 'La cantidad de sacos debe ser un valor numérico.',
            'quantity.min'          => 'La cantidad mínima es 0.01.',
            'weight_per_unit.min'   => 'El peso por unidad debe ser mayor a 0.',
            'final_weight.min'      => 'El peso final debe ser mayor a 0.',
            'warehouse_batch.max'   => 'El lote no debe superar 50 caracteres.',
            'comments.max'          => 'Los comentarios no deben superar 300 caracteres.',
        ];
    }
}
