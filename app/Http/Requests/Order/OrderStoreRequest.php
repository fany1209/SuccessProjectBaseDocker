<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            if ($key === 'pdf_file') {
                continue;
            }

            if (is_string($value)) {
                $sanitized[$key] = strip_tags(trim($value));
            } elseif (is_array($value)) {
                $sanitized[$key] = array_map(function ($item) {
                    if (is_string($item)) {
                        return strip_tags(trim($item));
                    }
                    if (is_array($item)) {
                        return array_map(function ($sub) {
                            return is_string($sub) ? strip_tags(trim($sub)) : $sub;
                        }, $item);
                    }
                    return $item;
                }, $value);
            }
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'año'                            => 'required|integer',
            'semana'                         => 'required|integer',
            'empresa'                        => 'required|string|max:255',
            'po'                             => 'nullable|string|max:100',
            'pdf_file'                       => 'nullable|file|mimes:pdf|max:10240',
            'items'                          => 'nullable|array',
            'items.*.producto'               => 'nullable|string|max:255',
            'items.*.cantidad'               => 'nullable|string|max:100',
            'producto'                       => 'nullable|string|max:255',
            'cantidad'                       => 'nullable|string|max:100',
            'documentacion_requerida'        => 'nullable|array',
            'documentacion_requerida.*'      => 'nullable|string|max:100',
            'fecha_de_carga'                 => 'nullable|date',
            'hora'                           => 'nullable|string|max:20',
            'fecha_de_envio'                 => 'nullable|date',
            'fecha_requerida_por_el_cliente' => 'nullable|date',
            'transporte'                     => 'nullable|string|max:100',
            'comentarios'                    => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'año.required'     => 'El año es obligatorio.',
            'semana.required'  => 'La semana es obligatoria.',
            'empresa.required' => 'La empresa es obligatoria.',
            'pdf_file.mimes'   => 'El archivo adjunto debe ser en formato PDF.',
            'pdf_file.max'     => 'El archivo PDF no debe exceder los 10MB.',
        ];
    }
}
