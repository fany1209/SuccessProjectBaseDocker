<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'internal_code' => is_string($this->internal_code) ? strip_tags(trim($this->internal_code)) : $this->internal_code,
            'name'          => is_string($this->name) ? strip_tags(trim($this->name)) : $this->name,
            'brand'         => is_string($this->brand) ? strip_tags(trim($this->brand)) : $this->brand,
            'status'        => is_string($this->status) ? strip_tags(trim($this->status)) : $this->status,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'internal_code' => ['nullable', 'string', 'max:100'],
            'brand'         => ['nullable', 'string', 'max:100'],
            'quantity'      => ['nullable', 'numeric', 'min:0'],
            'status'        => ['nullable', 'in:funcional,en reparacion,no funciona'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'El nombre del equipo es obligatorio.',
            'name.max'          => 'El nombre del equipo no puede exceder los 255 caracteres.',
            'internal_code.max' => 'El código interno no puede exceder los 100 caracteres.',
            'brand.max'         => 'La marca no puede exceder los 100 caracteres.',
            'quantity.numeric'  => 'La cantidad debe ser un valor numérico.',
            'quantity.min'      => 'La cantidad no puede ser menor a cero.',
            'status.in'         => 'El estado seleccionado no es válido (debe ser funcional, en reparacion o no funciona).',
        ];
    }
}
