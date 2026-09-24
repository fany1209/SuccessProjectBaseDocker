<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? strip_tags(trim($this->name)) : $this->name,
            'phone' => is_string($this->phone) ? strip_tags(trim($this->phone)) : $this->phone,
            'email' => is_string($this->email) ? strip_tags(trim($this->email)) : $this->email,
            'message' => is_string($this->message) ? strip_tags(trim($this->message)) : $this->message,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto válido.',
            'name.max' => 'El nombre no puede superar los 200 caracteres.',
            'phone.required' => 'El número de teléfono es obligatorio.',
            'phone.string' => 'El número de teléfono debe ser texto válido.',
            'phone.max' => 'El número de teléfono no puede superar los 20 caracteres.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.string' => 'El mensaje debe ser texto válido.',
            'message.max' => 'El mensaje no puede superar los 1000 caracteres.',
        ];
    }
}
