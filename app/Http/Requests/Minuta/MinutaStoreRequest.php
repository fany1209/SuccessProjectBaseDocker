<?php

namespace App\Http\Requests\Minuta;

use Illuminate\Foundation\Http\FormRequest;

class MinutaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'lugar' => $this->filled('lugar') ? strip_tags(trim((string)$this->input('lugar'))) : null,
            'tema_general' => $this->filled('tema_general') ? strip_tags(trim((string)$this->input('tema_general'))) : null,
            'ponente' => $this->filled('ponente') ? strip_tags(trim((string)$this->input('ponente'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'fecha_hora' => ['required'],
            'tema_general' => ['required', 'string', 'max:255'],
            'lugar' => ['nullable', 'string', 'max:255'],
            'ponente' => ['nullable', 'string', 'max:255'],
            'asistente_nombre' => ['nullable', 'array'],
            'asistente_departamento' => ['nullable', 'array'],
            'tema_tratado' => ['nullable', 'array'],
            'acuerdo' => ['nullable', 'array'],
            'responsable' => ['nullable', 'array'],
            'fecha_compromiso' => ['nullable', 'array'],
            'fecha_cierre' => ['nullable', 'array'],
            'estatus' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_hora.required' => 'La fecha y hora de la minuta son obligatorias.',
            'tema_general.required' => 'El tema general es obligatorio.',
            'tema_general.string' => 'El tema general debe ser texto.',
            'tema_general.max' => 'El tema general no puede superar los 255 caracteres.',
            'lugar.max' => 'El lugar no puede superar los 255 caracteres.',
            'ponente.max' => 'El ponente no puede superar los 255 caracteres.',
        ];
    }
}
