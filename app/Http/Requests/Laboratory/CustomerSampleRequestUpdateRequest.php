<?php

namespace App\Http\Requests\Laboratory;

use Illuminate\Foundation\Http\FormRequest;

class CustomerSampleRequestUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        $stringFields = [
            'cliente_nombre',
            'cliente_direccion',
            'cliente_correo',
            'cliente_telefono',
            'cliente_estatus',
            'paq_nombre',
            'paq_guia',
            'observaciones',
            'solicitante_nombre',
            'entrega_otro_txt',
        ];

        foreach ($stringFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $sanitized[$field] = strip_tags(trim($this->input($field)));
            }
        }

        if ($this->has('items') && is_array($this->input('items'))) {
            $items = $this->input('items');
            foreach ($items as $index => $item) {
                if (is_array($item)) {
                    foreach (['sku', 'um', 'cantidad', 'pres_otro_txt', 'lote_almacen', 'lote_venta', 'docs_otro_txt'] as $subField) {
                        if (isset($item[$subField]) && is_string($item[$subField])) {
                            $items[$index][$subField] = strip_tags(trim($item[$subField]));
                        }
                    }
                }
            }
            $sanitized['items'] = $items;
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    public function rules(): array
    {
        return [
            'fecha_solicitud'            => ['nullable', 'date'],
            'fecha_recoleccion'          => ['nullable', 'date'],
            'cliente_nombre'             => ['required', 'string', 'max:255'],
            'cliente_direccion'          => ['nullable', 'string'],
            'cliente_correo'             => ['nullable', 'email'],
            'cliente_telefono'           => ['nullable', 'string'],
            'cliente_estatus'            => ['nullable', 'string'],
            'paq_nombre'                 => ['nullable', 'string'],
            'paq_guia'                   => ['nullable', 'string'],
            'observaciones'              => ['nullable', 'string'],
            'solicitante_nombre'         => ['nullable', 'string'],
            'entrega_otro_txt'           => ['nullable', 'string'],
            'entrega_paqueteria'         => ['nullable'],
            'entrega_personal_empresa'   => ['nullable'],
            'entrega_recoleccion_planta' => ['nullable'],
            'entrega_otro'               => ['nullable'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.product_id'         => ['required', 'exists:products,product_id'],
            'items.*.sku'                => ['nullable', 'string', 'max:100'],
            'items.*.um'                 => ['nullable', 'string', 'max:50'],
            'items.*.cantidad'           => ['nullable', 'max:100'],
            'items.*.pres_ziploc'        => ['nullable'],
            'items.*.pres_whirlpak'      => ['nullable'],
            'items.*.pres_metalizada'    => ['nullable'],
            'items.*.pres_frasco'        => ['nullable'],
            'items.*.pres_bidon'         => ['nullable'],
            'items.*.pres_otro'          => ['nullable'],
            'items.*.pres_otro_txt'      => ['nullable', 'string', 'max:255'],
            'items.*.lote_almacen'       => ['nullable', 'string', 'max:100'],
            'items.*.lote_venta'         => ['nullable', 'string', 'max:100'],
            'items.*.docs_cc'            => ['nullable'],
            'items.*.docs_ft'            => ['nullable'],
            'items.*.docs_hs'            => ['nullable'],
            'items.*.docs_otro'          => ['nullable'],
            'items.*.docs_otro_txt'      => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_nombre.required'     => 'El nombre del cliente es obligatorio.',
            'items.required'              => 'Debe incluir al menos un producto solicitado.',
            'items.min'                   => 'Debe incluir al menos un producto solicitado.',
            'items.*.product_id.required' => 'El producto es obligatorio.',
            'items.*.product_id.exists'   => 'El producto seleccionado no existe.',
        ];
    }
}
