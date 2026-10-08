<?php

namespace App\Http\Requests\PortalUser;

use Illuminate\Foundation\Http\FormRequest;

class PortalUserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('nombre_contacto')) {
            $merge['nombre_contacto'] = strip_tags(trim((string)$this->input('nombre_contacto')));
        }
        if ($this->has('empresa')) {
            $merge['empresa'] = strip_tags(trim((string)$this->input('empresa')));
        }
        if ($this->has('email')) {
            $merge['email'] = strip_tags(trim(strtolower((string)$this->input('email'))));
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'customer_id'     => ['required', 'integer', 'exists:customers,customer_id'],
            'nombre_contacto' => ['required', 'string', 'max:150'],
            'empresa'         => ['required', 'string', 'max:200'],
            'email'           => ['required', 'email', 'max:255', 'unique:portal_users,email'],
            'password'        => ['required', 'string', 'min:6', 'confirmed'],
            'is_active'       => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required'     => 'Debe vincular el usuario a un cliente del sistema.',
            'customer_id.exists'       => 'El cliente seleccionado no existe en el sistema.',
            'nombre_contacto.required' => 'El nombre del contacto es obligatorio.',
            'nombre_contacto.max'      => 'El nombre del contacto no puede exceder los 150 caracteres.',
            'empresa.required'         => 'El nombre de la empresa es obligatorio.',
            'empresa.max'              => 'El nombre de la empresa no puede exceder los 200 caracteres.',
            'email.required'           => 'El correo electrónico es obligatorio.',
            'email.email'              => 'Debe proporcionar un correo electrónico válido.',
            'email.unique'             => 'El correo electrónico ya se encuentra registrado.',
            'password.required'        => 'La contraseña es obligatoria.',
            'password.min'             => 'La contraseña debe contener al menos 6 caracteres.',
            'password.confirmed'       => 'La confirmación de la contraseña no coincide.',
        ];
    }
}
