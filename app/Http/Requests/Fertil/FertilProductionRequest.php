<?php

namespace App\Http\Requests\Fertil;

use Illuminate\Foundation\Http\FormRequest;

class FertilProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'descripcion' => is_string($this->descripcion) ? strip_tags(trim($this->descripcion)) : $this->descripcion,
        ]);
    }

    public function rules(): array
    {
        return [
            'fecha_preparacion' => ['nullable', 'date'],
            'kg_preparados'     => ['nullable', 'numeric', 'min:0'],
            'fecha_ensacado'    => ['nullable', 'date'],
            'kg_ensacados'      => ['nullable', 'numeric', 'min:0'],
            'num_sacos'         => ['nullable', 'integer', 'min:0'],
            'descripcion'       => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_preparacion.date' => 'La fecha de preparación debe ser una fecha válida.',
            'kg_preparados.numeric'  => 'Los kg preparados deben ser un valor numérico.',
            'kg_preparados.min'      => 'Los kg preparados no pueden ser negativos.',
            'fecha_ensacado.date'    => 'La fecha de ensacado debe ser una fecha válida.',
            'kg_ensacados.numeric'   => 'Los kg ensacados deben ser un valor numérico.',
            'kg_ensacados.min'       => 'Los kg ensacados no pueden ser negativos.',
            'num_sacos.integer'      => 'El número de sacos debe ser un número entero.',
            'num_sacos.min'          => 'El número de sacos no puede ser negativo.',
            'descripcion.max'        => 'La descripción no puede exceder los 1000 caracteres.',
        ];
    }
}
