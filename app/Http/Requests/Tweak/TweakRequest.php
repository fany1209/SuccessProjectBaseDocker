<?php

namespace App\Http\Requests\Tweak;

use Illuminate\Foundation\Http\FormRequest;

class TweakRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva anti Stored-XSS antes de validar los datos.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        if ($this->has('comments') && is_string($this->input('comments'))) {
            $sanitized['comments'] = strip_tags(trim($this->input('comments')));
        }

        if ($this->has('type') && is_string($this->input('type'))) {
            $sanitized['type'] = strip_tags(trim($this->input('type')));
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para registro de ajustes de inventario.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type'         => ['required', 'string', 'in:Input,Output'],
            'quantity'     => ['required', 'numeric', 'gt:0'],
            'comments'     => ['nullable', 'string', 'max:300'],
            'inventory_id' => ['required', 'integer', 'exists:inventory,inventory_id'],
        ];
    }

    /**
     * Mensajes descriptivos en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required'         => 'El tipo de ajuste es obligatorio.',
            'type.in'               => 'El tipo de ajuste debe ser Input o Output.',
            'quantity.required'     => 'La cantidad del ajuste es obligatoria.',
            'quantity.numeric'      => 'La cantidad debe ser un valor numérico.',
            'quantity.gt'           => 'La cantidad debe ser mayor a cero.',
            'comments.string'       => 'Los comentarios deben ser una cadena de texto.',
            'comments.max'          => 'Los comentarios no deben superar los 300 caracteres.',
            'inventory_id.required' => 'El lote/registro de inventario es obligatorio.',
            'inventory_id.integer'  => 'El ID de inventario debe ser un número entero.',
            'inventory_id.exists'   => 'El registro de inventario seleccionado no existe.',
        ];
    }
}
