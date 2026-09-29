<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class InventoryUpdateCommentRequest extends FormRequest
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
        if ($this->has('comments') && is_string($this->comments)) {
            $this->merge([
                'comments' => strip_tags(trim($this->comments)),
            ]);
        }
    }

    /**
     * Reglas de validación para actualizar comentarios de movimiento.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'comments' => ['nullable', 'string', 'max:1000'],
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
            'comments.max'    => 'Los comentarios no deben superar los 1000 caracteres.',
            'comments.string' => 'Los comentarios deben ser texto.',
        ];
    }
}
