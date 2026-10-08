<?php

namespace App\Http\Requests\PortalPasswordReset;

use Illuminate\Foundation\Http\FormRequest;

class PortalChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password'          => ['required', 'string'],
            'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'          => 'La contraseña actual es obligatoria.',
            'new_password.required'              => 'La nueva contraseña es obligatoria.',
            'new_password.min'                   => 'La nueva contraseña debe contener al menos 8 caracteres.',
            'new_password.confirmed'             => 'La confirmación de la nueva contraseña no coincide.',
            'new_password_confirmation.required' => 'Debe confirmar su nueva contraseña.',
        ];
    }
}
