<?php

namespace App\Http\Requests\ProductInf;

use Illuminate\Foundation\Http\FormRequest;

class ProductInfRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole(['Admin', 'Warehouse'])) {
            return true;
        }

        return $user->can('product_inf.show');
    }

    /**
     * Sanitización defensiva previa a la validación para prevenir Stored-XSS.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'description'   => strip_tags(trim((string) $this->input('description', ''))),
            'bag_number'    => strip_tags(trim((string) $this->input('bag_number', ''))),
            'batch'         => strip_tags(trim((string) $this->input('batch', ''))),
            'location_name' => strip_tags(trim((string) $this->input('location_name', ''))),
        ]);
    }

    /**
     * Reglas de validación aplicables a la generación del código QR.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cli_id'        => ['nullable', 'integer', 'exists:cli,cli_id'],
            'inventory_id'  => ['nullable', 'integer', 'exists:inventory,inventory_id'],
            'product_id'    => ['nullable', 'integer', 'exists:products,product_id'],
            'location_id'   => ['nullable', 'integer', 'exists:locations,location_id'],
            'bag_number'    => ['nullable', 'string', 'max:100'],
            'batch'         => ['nullable', 'string', 'max:100'],
            'location_name' => ['nullable', 'string', 'max:100'],
            'description'   => ['required', 'string', 'min:3', 'max:2500'],
        ];
    }

    /**
     * Mensajes de error personalizados en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'description.required' => 'La descripción a detalle del producto es obligatoria.',
            'description.min'      => 'La descripción a detalle debe contener al menos 3 caracteres.',
            'description.max'      => 'La descripción no puede exceder los 2500 caracteres.',
            'cli_id.exists'        => 'El registro de producto en ubicación no existe o fue removido.',
            'product_id.exists'    => 'El producto seleccionado no existe en el catálogo.',
            'inventory_id.exists'  => 'El lote de inventario especificado no es válido.',
            'location_id.exists'   => 'La ubicación seleccionada no es válida.',
        ];
    }
}
