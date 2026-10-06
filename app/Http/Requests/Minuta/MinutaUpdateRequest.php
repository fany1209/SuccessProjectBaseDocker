<?php

namespace App\Http\Requests\Minuta;

use Illuminate\Foundation\Http\FormRequest;

class MinutaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('lugar')) {
            $merge['lugar'] = $this->filled('lugar') ? strip_tags(trim((string)$this->input('lugar'))) : null;
        }
        if ($this->has('tema_general')) {
            $merge['tema_general'] = $this->filled('tema_general') ? strip_tags(trim((string)$this->input('tema_general'))) : null;
        }
        if ($this->has('ponente')) {
            $merge['ponente'] = $this->filled('ponente') ? strip_tags(trim((string)$this->input('ponente'))) : null;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'fecha_hora' => ['nullable'],
            'tema_general' => ['nullable', 'string', 'max:255'],
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
            'tema_general.string' => 'El tema general debe ser texto.',
            'tema_general.max' => 'El tema general no puede superar los 255 caracteres.',
            'lugar.max' => 'El lugar no puede superar los 255 caracteres.',
            'ponente.max' => 'El ponente no puede superar los 255 caracteres.',
        ];
    }
}
