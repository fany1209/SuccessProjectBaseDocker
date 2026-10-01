<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReceptionRequest extends FormRequest
{
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

        $stringFields = [
            'folio_muestra',
            'sku',
            'batch',
            'nombre_comercial',
            'descripcion',
            'origen_muestra',
            'origen_otro',
            'objetivo_otro',
            'um',
            'um_otro',
            'observaciones_laboratorio',
            'firma_entrega_nombre',
            'firma_recepcion_nombre',
            'docs_otro_txt',
        ];

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
     * Reglas de validación para actualización de recepción de muestra.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'folio_muestra'             => ['nullable', 'string', 'max:100'],
            'product_id'                => ['nullable', 'integer', 'exists:products,product_id'],
            'sku'                       => ['nullable', 'string', 'max:100'],
            'batch'                     => ['nullable', 'string', 'max:100'],
            'nombre_comercial'          => ['nullable', 'string', 'max:255'],
            'fecha_entrada'             => ['nullable', 'date'],
            'fecha_caducidad'           => ['nullable', 'date'],
            'descripcion'               => ['nullable', 'string'],
            'supplier_id'               => ['nullable', 'integer', 'exists:suppliers,supplier_id'],
            'origen_muestra'            => ['nullable', 'string', 'in:proveedor,produccion,almacen,otro'],
            'origen_otro'               => ['nullable', 'string', 'max:255'],
            'objetivo_muestra'          => ['nullable', 'array'],
            'objetivo_muestra.*'        => ['in:inspeccion,retencion,analisis,desarrollo,exposicion,otro'],
            'objetivo_otro'             => ['nullable', 'string', 'max:255'],
            'cantidad'                  => ['nullable', 'numeric'],
            'um'                        => ['nullable', 'string', 'in:g,kg,l,ml,otro'],
            'um_otro'                   => ['nullable', 'string', 'max:50'],
            'docs_ccf'                  => ['nullable', 'boolean'],
            'docs_ft'                   => ['nullable', 'boolean'],
            'docs_hs'                   => ['nullable', 'boolean'],
            'docs_otro'                 => ['nullable', 'boolean'],
            'docs_otro_txt'             => ['nullable', 'string', 'max:255'],
            'observaciones_laboratorio' => ['nullable', 'string'],
            'firma_entrega_nombre'      => ['nullable', 'string', 'max:150'],
            'firma_recepcion_nombre'    => ['nullable', 'string', 'max:150'],
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
            'product_id.exists'         => 'El producto seleccionado no existe en el catálogo.',
            'supplier_id.exists'        => 'El proveedor seleccionado no existe en el catálogo.',
            'fecha_entrada.date'        => 'La fecha de entrada no tiene un formato válido.',
            'fecha_caducidad.date'      => 'La fecha de caducidad no tiene un formato válido.',
            'cantidad.numeric'          => 'La cantidad debe ser un número válido.',
            'origen_muestra.in'         => 'El origen de muestra seleccionado no es válido.',
            'um.in'                     => 'La unidad de medida seleccionada no es válida.',
        ];
    }
}
