<?php

namespace App\Http\Requests\Prospect;

use Illuminate\Foundation\Http\FormRequest;

class ProspectStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = strip_tags(trim($value));
            }
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'sector_id' => 'required|integer|exists:sectors,sector_id',
            'name'      => 'required|string|max:200',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:150',
            'rfc'       => 'nullable|string|max:13',
            'state'     => 'nullable|string|max:80',
            'city'      => 'nullable|string|max:80',
            'district'  => 'nullable|string|max:80',
            'address'   => 'nullable|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'sector_id.required' => 'El sector es obligatorio.',
            'sector_id.exists'   => 'El sector seleccionado no existe.',
            'name.required'      => 'El nombre del prospecto es obligatorio.',
            'name.max'           => 'El nombre no puede superar los 200 caracteres.',
            'email.email'        => 'El correo electrónico debe ser una dirección válida.',
            'email.max'          => 'El correo electrónico no puede superar los 150 caracteres.',
            'phone.max'          => 'El teléfono no puede superar los 20 caracteres.',
            'rfc.max'            => 'El RFC no puede superar los 13 caracteres.',
        ];
    }
}
