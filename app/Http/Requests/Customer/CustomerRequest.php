<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'rfc' => is_string($this->rfc) ? strip_tags(trim($this->rfc)) : $this->rfc,
            'postal_code' => is_string($this->postal_code) ? strip_tags(trim($this->postal_code)) : $this->postal_code,
            'state' => is_string($this->state) ? strip_tags(trim($this->state)) : $this->state,
            'city' => is_string($this->city) ? strip_tags(trim($this->city)) : $this->city,
            'district' => is_string($this->district) ? strip_tags(trim($this->district)) : $this->district,
            'address' => is_string($this->address) ? strip_tags(trim($this->address)) : $this->address,
            'country' => is_string($this->country) ? strip_tags(trim($this->country)) : $this->country,
            'contact' => is_string($this->contact) ? strip_tags(trim($this->contact)) : $this->contact,
            'delivery_address' => is_string($this->delivery_address) ? strip_tags(trim($this->delivery_address)) : $this->delivery_address,
            'vendedor' => is_string($this->vendedor) ? strip_tags(trim($this->vendedor)) : $this->vendedor,
        ]);
    }

    public function rules(): array
    {
        return [
            'sector_id' => ['required', 'exists:sectors,sector_id'],
            'name' => ['required', 'string', 'max:200'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'rfc' => ['nullable', 'string', 'max:13'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'state' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:200'],
            'country' => ['nullable', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:150'],
            'delivery_address' => ['nullable', 'string'],
            'vendedor' => ['nullable', 'string', 'max:150'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,customer_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'sector_id.required' => 'El sector es obligatorio.',
            'sector_id.exists' => 'El sector seleccionado no es válido.',
            'name.required' => 'El nombre del cliente es obligatorio.',
            'name.max' => 'El nombre no puede exceder los 200 caracteres.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.max' => 'El correo electrónico no puede exceder los 150 caracteres.',
            'rfc.max' => 'El RFC no puede exceder los 13 caracteres.',
            'phone.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'postal_code.max' => 'El código postal no puede exceder los 10 caracteres.',
            'state.max' => 'El estado no puede exceder los 80 caracteres.',
            'city.max' => 'La ciudad no puede exceder los 80 caracteres.',
            'district.max' => 'La colonia no puede exceder los 80 caracteres.',
            'address.max' => 'La dirección no puede exceder los 200 caracteres.',
            'country.max' => 'El país no puede exceder los 100 caracteres.',
            'contact.max' => 'El contacto no puede exceder los 150 caracteres.',
        ];
    }
}
