<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('name')) {
            $merge['name'] = strip_tags(trim((string)$this->input('name')));
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
        $userId = auth()->id();

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'photo' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre es obligatorio.',
            'name.max'       => 'El nombre no puede exceder los 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Debe ingresar un correo electrónico válido.',
            'email.unique'   => 'El correo electrónico ya se encuentra registrado por otro usuario.',
            'photo.image'    => 'La foto debe ser un archivo de imagen válido.',
            'photo.mimes'    => 'La foto debe tener formato jpeg, jpg, png o webp.',
            'photo.max'      => 'La foto no puede pesar más de 2MB.',
        ];
    }
}
