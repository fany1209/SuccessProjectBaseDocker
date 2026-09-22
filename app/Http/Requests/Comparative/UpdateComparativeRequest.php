<?php

namespace App\Http\Requests\Comparative;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComparativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitizeArray = function ($input) {
            if (!is_array($input)) {
                return $input;
            }
            return array_map(function ($val) {
                return is_string($val) ? strip_tags(trim($val)) : $val;
            }, $input);
        };

        $this->merge([
            'insumo' => $sanitizeArray($this->insumo),
            'proveedor' => $sanitizeArray($this->proveedor),
            'descripcion' => $sanitizeArray($this->descripcion),
            'comentarios' => $sanitizeArray($this->comentarios),
            'entrega_estimada' => $sanitizeArray($this->entrega_estimada),
            'link' => $sanitizeArray($this->link),
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'array'],
            'id.*' => ['nullable', 'integer'],
            'insumo' => ['required', 'array', 'min:1'],
            'insumo.*' => ['required', 'string', 'max:255'],
            'cantidad' => ['nullable', 'array'],
            'cantidad.*' => ['nullable', 'numeric', 'min:0'],
            'proveedor' => ['nullable', 'array'],
            'proveedor.*' => ['nullable', 'string', 'max:255'],
            'precio_total' => ['nullable', 'array'],
            'precio_total.*' => ['nullable', 'numeric', 'min:0'],
            'imagen' => ['nullable', 'array'],
            'imagen.*' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'array'],
            'descripcion.*' => ['nullable', 'string'],
            'comentarios' => ['nullable', 'array'],
            'comentarios.*' => ['nullable', 'string'],
            'entrega_estimada' => ['nullable', 'array'],
            'entrega_estimada.*' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'array'],
            'link.*' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'insumo.required' => 'Debe registrar al menos un insumo.',
            'insumo.array' => 'El campo insumos debe ser una lista.',
            'insumo.min' => 'Debe ingresar al menos un insumo en la comparativa.',
            'insumo.*.required' => 'El nombre del insumo es obligatorio.',
            'insumo.*.string' => 'El insumo debe ser texto válido.',
            'insumo.*.max' => 'El insumo no puede superar los 255 caracteres.',
            'cantidad.*.numeric' => 'La cantidad debe ser un valor numérico.',
            'cantidad.*.min' => 'La cantidad no puede ser negativa.',
            'proveedor.*.max' => 'El proveedor no puede exceder los 255 caracteres.',
            'precio_total.*.numeric' => 'El precio total debe ser numérico.',
        ];
    }
}
