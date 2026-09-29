<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class InventoryTransactionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva anti Stored-XSS y normalización de cantidades.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        if ($this->has('quantity') && is_array($this->quantity)) {
            $sanitized['quantity'] = array_map(function ($q) {
                return is_string($q) ? str_replace(',', '.', trim($q)) : $q;
            }, $this->quantity);
        }

        $stringFields = [
            'operator',
            'license_number',
            'security_seal_number',
            'unit_plates',
            'trailer_plates',
            'comments',
            'vendedor',
        ];

        foreach ($stringFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $sanitized[$field] = strip_tags(trim($this->input($field)));
            }
        }

        if ($this->has('warehouse_batch') && is_array($this->warehouse_batch)) {
            $sanitized['warehouse_batch'] = array_map(function ($b) {
                return (string) (is_string($b) ? strip_tags(trim($b)) : $b);
            }, $this->warehouse_batch);
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para transacciones de inventario (Entrada, Salida, Salida Interna).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type'                 => ['required', 'string', 'in:Input,Output,InternalOutput'],
            'transport_line'       => ['nullable', 'integer'],
            'operator'             => ['nullable', 'string', 'max:200'],
            'license_number'       => ['nullable', 'string', 'max:50'],
            'security_seal'        => ['required', 'integer'],
            'security_seal_number' => ['nullable', 'required_if:security_seal,1', 'string', 'max:50'],
            'unit_plates'          => ['nullable', 'string', 'max:20'],
            'trailer_plates'       => ['nullable', 'string', 'max:20'],
            'comments'             => ['nullable', 'string', 'max:300'],
            'supplier'             => ['nullable', 'required_if:type,Input', 'integer'],
            'customer'             => ['nullable', 'required_if:type,Output', 'integer'],
            'vendedor'             => ['nullable', 'required_if:type,Output', 'string', 'max:200'],
            'product_id'           => ['required', 'array', 'min:1'],
            'product_id.*'         => ['required', 'integer'],
            'quantity'             => ['required', 'array', 'min:1'],
            'quantity.*'           => ['required', 'numeric', 'min:0.001'],
            'warehouse_batch'      => ['required', 'array', 'min:1'],
            'warehouse_batch.*'    => ['required', 'string', 'max:50'],
            'location_id'          => ['nullable', 'array'],
            'location_id.*'        => ['nullable', 'integer'],
            'concept_id'           => ['nullable', 'array'],
            'concept_id.*'         => ['nullable', 'integer'],
            'weight_per_unit'      => ['nullable', 'array'],
            'weight_per_unit.*'    => ['nullable', 'numeric'],
            'bag_number'           => ['nullable', 'array'],
            'protein'              => ['nullable', 'array'],
            'bag_weight'           => ['nullable', 'array'],
            'bag_location_id'      => ['nullable', 'array'],
            'output_location_id'   => ['nullable', 'array'],
            'label_batch'          => ['nullable', 'array'],
            'req_id'               => ['nullable', 'integer'],
        ];
    }

    /**
     * Mensajes claros y legibles en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required'                 => 'El tipo de transacción es obligatorio.',
            'type.in'                       => 'El tipo de transacción debe ser Input, Output o InternalOutput.',
            'security_seal.required'        => 'El sello de seguridad es obligatorio.',
            'security_seal_number.required_if' => 'El número de sello de seguridad es obligatorio cuando tiene sello.',
            'supplier.required_if'          => 'El proveedor es obligatorio para una entrada de inventario.',
            'customer.required_if'          => 'El cliente es obligatorio para una salida de inventario.',
            'vendedor.required_if'          => 'El vendedor es obligatorio para una salida de inventario.',
            'product_id.required'           => 'Debe incluir al menos un producto.',
            'product_id.array'              => 'La lista de productos es inválida.',
            'product_id.min'                => 'Debe incluir al menos un producto.',
            'product_id.*.required'         => 'El identificador del producto es obligatorio.',
            'quantity.required'             => 'Debe especificar las cantidades.',
            'quantity.*.required'           => 'La cantidad del producto es obligatoria.',
            'quantity.*.numeric'            => 'La cantidad debe ser numérica.',
            'quantity.*.min'                => 'La cantidad mínima por producto es 0.001.',
            'warehouse_batch.required'      => 'El lote de almacén es obligatorio.',
            'warehouse_batch.*.required'    => 'Cada producto debe tener un lote especificado.',
            'comments.max'                  => 'Los comentarios no pueden exceder 300 caracteres.',
        ];
    }
}
