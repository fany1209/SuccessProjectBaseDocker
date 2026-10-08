<?php

namespace App\Http\Requests\PortalPasswordReset;

use Illuminate\Foundation\Http\FormRequest;

class PortalResetPasswordRequest extends FormRequest
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
            'token'                 => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'                 => 'El token de restablecimiento es obligatorio.',
            'email.required'                 => 'El correo electrónico es obligatorio.',
            'email.email'                    => 'Debe ingresar un correo electrónico válido.',
            'password.required'              => 'La contraseña es obligatoria.',
            'password.min'                   => 'La contraseña debe contener al menos 8 caracteres.',
            'password.confirmed'             => 'La confirmación de la contraseña no coincide.',
            'password_confirmation.required' => 'Debe confirmar su contraseña.',
        ];
    }
}
