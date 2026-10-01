<?php

namespace App\Http\Requests\Laboratory;

use Illuminate\Foundation\Http\FormRequest;

class SalidaMuestraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        $stringFields = [
            'folio_muestra',
            'nombre_comercial',
            'sku',
            'lote',
            'um',
            'cantidad',
            'descripcion',
            'motivo_salida',
            'motivo_otro',
            'entrega_otro_txt',
            'paq_empresa',
            'paq_guia',
            'dest_nombre',
            'dest_direccion',
            'dest_recibe',
            'dest_correo',
            'dest_telefono',
            'docs_otro_txt',
            'observaciones',
            'solicitante_nombre',
            'recolector_nombre',
            'autoriza_nombre',
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

    public function rules(): array
    {
        return [
            'folio_muestra'              => ['nullable', 'string', 'max:50'],
            'product_id'                 => ['required', 'integer', 'exists:products,product_id'],
            'fecha_salida'               => ['nullable', 'date'],
            'nombre_comercial'           => ['nullable', 'string', 'max:255'],
            'sku'                        => ['nullable', 'string', 'max:100'],
            'lote'                       => ['nullable', 'string', 'max:100'],
            'um'                         => ['nullable', 'string', 'max:50'],
            'cantidad'                   => ['nullable', 'string', 'max:50'],
            'descripcion'                => ['nullable', 'string', 'max:2000'],
            'motivo_salida'              => ['nullable', 'in:cliente,analisis,desarrollo,caducado,exposicion,otro'],
            'motivo_otro'                => ['nullable', 'string', 'max:255'],
            'entrega_paqueteria'         => ['nullable'],
            'entrega_recoleccion_planta' => ['nullable'],
            'entrega_personal_empresa'   => ['nullable'],
            'entrega_otro'               => ['nullable'],
            'entrega_otro_txt'           => ['nullable', 'string', 'max:255'],
            'paq_empresa'                => ['nullable', 'string', 'max:255'],
            'paq_guia'                   => ['nullable', 'string', 'max:100'],
            'dest_nombre'                => ['nullable', 'string', 'max:255'],
            'dest_direccion'             => ['nullable', 'string', 'max:500'],
            'dest_recibe'                => ['nullable', 'string', 'max:255'],
            'dest_correo'                => ['nullable', 'email', 'max:255'],
            'dest_telefono'              => ['nullable', 'string', 'max:50'],
            'docs_cc'                    => ['nullable'],
            'docs_ft'                    => ['nullable'],
            'docs_hs'                    => ['nullable'],
            'docs_otro'                  => ['nullable'],
            'docs_otro_txt'              => ['nullable', 'string', 'max:255'],
            'observaciones'              => ['nullable', 'string', 'max:5000'],
            'solicitante_nombre'         => ['nullable', 'string', 'max:255'],
            'recolector_nombre'          => ['nullable', 'string', 'max:255'],
            'autoriza_nombre'            => ['nullable', 'string', 'max:255'],
        ];
    }
}
