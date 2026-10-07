<?php

namespace App\Http\Requests\YeastProduction;

use Illuminate\Foundation\Http\FormRequest;

class YeastProductionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'internal_weight' => ['nullable', 'numeric', 'min:0'],
            'external_weight' => ['nullable', 'numeric', 'min:0'],
            'bags_natural' => ['nullable', 'integer', 'min:0'],
            'bags_mix' => ['nullable', 'integer', 'min:0'],
            'bags_white' => ['nullable', 'integer', 'min:0'],
            'finished_product_kg' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'internal_weight.numeric' => 'El peso interno debe ser un número válido.',
            'internal_weight.min' => 'El peso interno no puede ser negativo.',
            'external_weight.numeric' => 'El peso externo debe ser un número válido.',
            'external_weight.min' => 'El peso externo no puede ser negativo.',
            'bags_natural.integer' => 'El número de sacos naturales debe ser entero.',
            'bags_natural.min' => 'El número de sacos naturales no puede ser negativo.',
            'bags_mix.integer' => 'El número de sacos mix debe ser entero.',
            'bags_mix.min' => 'El número de sacos mix no puede ser negativo.',
            'bags_white.integer' => 'El número de sacos blancos debe ser entero.',
            'bags_white.min' => 'El número de sacos blancos no puede ser negativo.',
            'finished_product_kg.numeric' => 'Los kilogramos de producto terminado deben ser un número.',
            'finished_product_kg.min' => 'Los kilogramos no pueden ser negativos.',
        ];
    }
}
