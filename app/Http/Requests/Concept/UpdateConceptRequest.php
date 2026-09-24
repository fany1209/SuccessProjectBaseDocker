<?php

namespace App\Http\Requests\Concept;

use App\Models\Concept;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConceptRequest extends FormRequest
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
        $conceptParam = $this->route('concept') ?? $this->route('id') ?? $this->id;
        $conceptId = $conceptParam instanceof Concept ? $conceptParam->concept_id : $conceptParam;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('concepts', 'name')->ignore($conceptId, 'concept_id'),
            ],
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
