<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class InventoryUpdateDateRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar fecha de movimiento.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type'       => ['required', 'string', 'in:input,output,Input,Output'],
            'id'         => ['required', 'integer'],
            'updated_at' => ['required', 'date'],
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
            'type.required'       => 'El tipo de movimiento es obligatorio.',
            'type.in'             => 'El tipo debe ser input u output.',
            'id.required'         => 'El identificador del movimiento es obligatorio.',
            'id.integer'          => 'El identificador debe ser numérico.',
            'updated_at.required' => 'La nueva fecha es obligatoria.',
            'updated_at.date'     => 'La fecha no tiene un formato válido.',
        ];
    }
}
