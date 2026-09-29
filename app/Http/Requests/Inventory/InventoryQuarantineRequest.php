<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class InventoryQuarantineRequest extends FormRequest
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
        if ($this->has('notes') && is_string($this->notes)) {
            $this->merge([
                'notes' => strip_tags(trim($this->notes)),
            ]);
        }
    }

    /**
     * Reglas de validación para envío y retiro de cuarentena.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Si viene quarantine_id, es retiro de cuarentena; si viene inventory_id, es ingreso.
        $isAdd = $this->has('inventory_id');

        if ($isAdd) {
            return [
                'inventory_id' => ['required', 'integer', 'exists:inventory,inventory_id'],
                'quantity'     => ['required', 'numeric', 'min:0.001'],
                'notes'        => ['required', 'string', 'max:350'],
            ];
        }

        return [
            'quarantine_id' => ['required', 'integer', 'exists:quarantine,quarantine_id'],
            'quantity'      => ['required', 'numeric', 'min:0.001'],
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
            'inventory_id.required'  => 'El registro de inventario es obligatorio.',
            'inventory_id.integer'   => 'El identificador de inventario debe ser numérico.',
            'inventory_id.exists'    => 'El inventario seleccionado no existe.',
            'quarantine_id.required' => 'El registro de cuarentena es obligatorio.',
            'quarantine_id.integer'  => 'El identificador de cuarentena debe ser numérico.',
            'quarantine_id.exists'   => 'El registro de cuarentena seleccionado no existe.',
            'quantity.required'      => 'La cantidad es obligatoria.',
            'quantity.numeric'       => 'La cantidad debe ser numérica.',
            'quantity.min'           => 'La cantidad mínima es 0.001.',
            'notes.required'         => 'Las notas u observaciones son obligatorias.',
            'notes.string'           => 'Las notas deben ser una cadena de texto.',
            'notes.max'              => 'Las notas no deben superar los 350 caracteres.',
        ];
    }
}
