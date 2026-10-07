<?php

namespace App\Http\Requests\Vitayela;

use Illuminate\Foundation\Http\FormRequest;

class VitayelaProductionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('descripcion')) {
            $this->merge([
                'descripcion' => $this->filled('descripcion') ? strip_tags(trim((string)$this->input('descripcion'))) : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'fecha_preparacion' => ['nullable', 'date'],
            'kg_preparados' => ['nullable', 'numeric', 'min:0'],
            'fecha_ensacado' => ['nullable', 'date'],
            'kg_ensacados' => ['nullable', 'numeric', 'min:0'],
            'num_sacos' => ['nullable', 'integer', 'min:0'],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_preparacion.date' => 'La fecha de preparación debe ser válida.',
            'kg_preparados.numeric' => 'Los kilogramos preparados deben ser un número.',
            'kg_preparados.min' => 'Los kilogramos preparados no pueden ser negativos.',
            'fecha_ensacado.date' => 'La fecha de ensacado debe ser válida.',
            'kg_ensacados.numeric' => 'Los kilogramos ensacados deben ser un número.',
            'kg_ensacados.min' => 'Los kilogramos ensacados no pueden ser negativos.',
            'num_sacos.integer' => 'El número de sacos debe ser un número entero.',
            'num_sacos.min' => 'El número de sacos no puede ser negativo.',
        ];
    }
}
