<?php

namespace App\Http\Requests\PortalPasswordReset;

use Illuminate\Foundation\Http\FormRequest;

class PortalForgotPasswordRequest extends FormRequest
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
            'email' => ['required', 'email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Debe ingresar un correo electrónico válido.',
        ];
    }
}
