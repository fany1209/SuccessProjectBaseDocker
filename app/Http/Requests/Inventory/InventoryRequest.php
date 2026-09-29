<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva anti Stored-XSS antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        if ($this->has('batch') && is_string($this->batch)) {
            $sanitized['batch'] = strip_tags(trim($this->batch));
        }

        if ($this->has('bar_code') && is_string($this->bar_code)) {
            $sanitized['bar_code'] = strip_tags(trim($this->bar_code));
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para registro y actualización de inventario.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('POST');
        $inventoryParam = $this->route('inventory') ?? $this->route('id');
        $inventoryId = is_object($inventoryParam) ? ($inventoryParam->inventory_id ?? $inventoryParam->id) : $inventoryParam;

        return [
            'product_id' => [$isPost ? 'required' : 'sometimes', 'required', 'integer', 'exists:products,product_id'],
            'stock'      => [$isPost ? 'required' : 'sometimes', 'required', 'numeric', 'min:0'],
            'batch'      => [
                $isPost ? 'required' : 'sometimes',
                'required',
                'string',
                'max:30',
                $isPost ? 'unique:inventory,batch' : 'unique:inventory,batch,' . $inventoryId . ',inventory_id',
            ],
            'bar_code'   => ['nullable', 'integer'],
        ];
    }

    /**
     * Mensajes de error en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'El producto es obligatorio.',
            'product_id.integer'  => 'El identificador del producto debe ser numérico.',
            'product_id.exists'   => 'El producto seleccionado no existe.',
            'stock.required'      => 'El stock es obligatorio.',
            'stock.numeric'       => 'El stock debe ser un valor numérico.',
            'stock.min'           => 'El stock no puede ser negativo.',
            'batch.required'      => 'El lote es obligatorio.',
            'batch.string'        => 'El lote debe ser una cadena de texto.',
            'batch.max'           => 'El lote no debe superar los 30 caracteres.',
            'batch.unique'        => 'Ya existe un registro de inventario con este lote.',
            'bar_code.integer'    => 'El código de barras debe ser numérico.',
        ];
    }
}
