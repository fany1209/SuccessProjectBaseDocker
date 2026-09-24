<?php

namespace App\Http\Requests\Concept;

use Illuminate\Foundation\Http\FormRequest;

class StoreConceptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? strip_tags(trim($this->name)) : $this->name,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:concepts,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del concepto es obligatorio.',
            'name.string' => 'El nombre del concepto debe ser texto válido.',
            'name.max' => 'El nombre del concepto no puede superar los 255 caracteres.',
            'name.unique' => 'Ya existe un concepto registrado con este nombre.',
        ];
    }
}
