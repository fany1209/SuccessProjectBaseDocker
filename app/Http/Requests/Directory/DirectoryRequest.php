<?php

namespace App\Http\Requests\Directory;

use Illuminate\Foundation\Http\FormRequest;

class DirectoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'Name' => is_string($this->Name) ? strip_tags(trim($this->Name)) : $this->Name,
            'Product' => is_string($this->Product) ? strip_tags(trim($this->Product)) : $this->Product,
            'Address' => is_string($this->Address) ? strip_tags(trim($this->Address)) : $this->Address,
            'Phone' => is_string($this->Phone) ? strip_tags(trim($this->Phone)) : $this->Phone,
            'Email' => is_string($this->Email) ? strip_tags(trim($this->Email)) : $this->Email,
            'RFC' => is_string($this->RFC) ? strip_tags(trim($this->RFC)) : $this->RFC,
            'Contact' => is_string($this->Contact) ? strip_tags(trim($this->Contact)) : $this->Contact,
        ]);
    }

    public function rules(): array
    {
        $ignoreCode = $this->original_code ?? $this->route('code');

        $codeRule = ($this->isMethod('POST'))
            ? 'required|integer|unique:supplier_directory,Code_supplier'
            : 'required|integer|unique:supplier_directory,Code_supplier,' . $ignoreCode . ',Code_supplier';

        return [
            'Name' => ['required', 'string', 'max:255'],
            'Code_supplier' => $codeRule,
            'Product' => ['nullable', 'string', 'max:255'],
            'Address' => ['nullable', 'string'],
            'Phone' => ['nullable', 'string', 'max:20'],
            'Email' => ['nullable', 'email', 'max:100'],
            'RFC' => ['nullable', 'string', 'max:15'],
            'Contact' => ['nullable', 'string', 'max:255'],
            'original_code' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'Name.required' => 'El nombre del proveedor es obligatorio.',
            'Name.max' => 'El nombre del proveedor no puede exceder los 255 caracteres.',
            'Code_supplier.required' => 'El código de proveedor es obligatorio.',
            'Code_supplier.integer' => 'El código de proveedor debe ser un número entero.',
            'Code_supplier.unique' => 'Este código de proveedor ya se encuentra registrado.',
            'Email.email' => 'El correo electrónico no tiene un formato válido.',
            'Email.max' => 'El correo electrónico no puede exceder los 100 caracteres.',
            'Phone.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'RFC.max' => 'El RFC no puede exceder los 15 caracteres.',
            'Product.max' => 'El producto no puede exceder los 255 caracteres.',
            'Contact.max' => 'El contacto no puede exceder los 255 caracteres.',
        ];
    }
}
