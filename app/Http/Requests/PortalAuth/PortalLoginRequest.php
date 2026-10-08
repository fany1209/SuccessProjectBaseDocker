<?php

namespace App\Http\Requests\PortalAuth;

use Illuminate\Foundation\Http\FormRequest;

class PortalLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge([
                'email' => strip_tags(trim(strtolower((string)$this->input('email')))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Debe proporcionar un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }
}
