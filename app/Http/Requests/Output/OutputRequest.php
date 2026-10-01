<?php

namespace App\Http\Requests\Output;

use Illuminate\Foundation\Http\FormRequest;

class OutputRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva anti Stored-XSS y normalización previa a validación.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        $textFields = [
            'operator',
            'license_number',
            'security_seal_number',
            'unit_plates',
            'trailer_plates',
            'comments',
            'vendedor',
        ];

        foreach ($textFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $sanitized[$field] = strip_tags(trim($this->input($field)));
            }
        }

        // Normalización de array de cantidades
        if ($this->has('quantity') && is_array($this->input('quantity'))) {
            $sanitized['quantity'] = array_map(function ($q) {
                if (is_string($q)) {
                    $q = trim($q);
                    $q = str_replace(',', '.', $q);
                }
                return $q;
            }, $this->input('quantity'));
        }

        // Normalización de lotes
        if ($this->has('label_batch') && is_array($this->input('label_batch'))) {
            $sanitized['label_batch'] = array_map(function ($batch) {
                return is_string($batch) ? strip_tags(trim($batch)) : $batch;
            }, $this->input('label_batch'));
        }

        // Determinar sello de seguridad
        if ($this->filled('security_seal_number')) {
            $sanitized['security_seal'] = 1;
        } elseif (!$this->has('security_seal')) {
            $sanitized['security_seal'] = 0;
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para registro y actualización de salidas de almacén.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'operator'             => ['required', 'string', 'max:200'],
            'license_number'       => ['required', 'string', 'max:50'],
            'security_seal'        => ['required', 'integer', 'in:0,1'],
            'security_seal_number' => ['nullable', 'string', 'max:50'],
            'unit_plates'          => ['required', 'string', 'max:20'],
            'trailer_plates'       => ['nullable', 'string', 'max:20'],
            'comments'             => ['nullable', 'string', 'max:300'],
            'customer_id'          => ['required', 'integer', 'exists:customers,customer_id'],
            'transport_line_id'    => ['required', 'integer', 'exists:transport_lines,transport_line_id'],
            'vendedor'             => ['required', 'string', 'max:200'],
            'product_id'           => ['required', 'array', 'min:1'],
            'product_id.*'         => ['required', 'integer', 'exists:products,product_id'],
            'quantity'             => ['required', 'array', 'min:1'],
            'quantity.*'           => ['required', 'numeric', 'min:0.001'],
            'label_batch'          => ['required', 'array', 'min:1'],
            'label_batch.*'        => ['required', 'string', 'max:50'],
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
            'operator.required'             => 'El nombre del operador es obligatorio.',
            'license_number.required'       => 'El número de licencia es obligatorio.',
            'security_seal.required'        => 'El estado del sello de seguridad es obligatorio.',
            'unit_plates.required'          => 'Las placas de la unidad son obligatorias.',
            'customer_id.required'          => 'El cliente es obligatorio.',
            'customer_id.exists'            => 'El cliente seleccionado no existe.',
            'transport_line_id.required'    => 'La línea de transporte es obligatoria.',
            'transport_line_id.exists'      => 'La línea de transporte no existe.',
            'vendedor.required'             => 'El vendedor es obligatorio.',
            'product_id.required'           => 'Debe especificar al menos un producto.',
            'product_id.min'                => 'Debe especificar al menos un producto.',
            'product_id.*.exists'           => 'Uno o más productos seleccionados no existen.',
            'quantity.required'             => 'Debe especificar la cantidad de cada producto.',
            'quantity.*.min'                => 'La cantidad de cada producto debe ser mayor a cero.',
            'label_batch.required'          => 'Debe especificar el lote de etiqueta de cada producto.',
            'label_batch.*.required'        => 'El lote de etiqueta no puede estar vacío.',
        ];
    }
}
