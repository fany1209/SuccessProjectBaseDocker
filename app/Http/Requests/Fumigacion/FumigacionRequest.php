<?php

namespace App\Http\Requests\Fumigacion;

use Illuminate\Foundation\Http\FormRequest;

class FumigacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                $clean = strip_tags(trim($value));
                $sanitized[$key] = $clean === '' ? null : $clean;
            }
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    public function rules(): array
    {
        return [
            'proveedor'         => ['required', 'string', 'max:255'],
            'fecha_programada'  => ['required', 'date'],
            'metodo_aplicacion' => ['nullable', 'string', 'max:100'],
            'estado'            => ['nullable', 'string', 'in:Pendiente,Realizado,Cancelado'],
            'observaciones'     => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'proveedor.required'        => 'El proveedor es obligatorio.',
            'proveedor.string'          => 'El proveedor debe ser una cadena de texto.',
            'proveedor.max'             => 'El proveedor no puede exceder los 255 caracteres.',
            'fecha_programada.required' => 'La fecha programada es obligatoria.',
            'fecha_programada.date'     => 'La fecha programada debe tener un formato de fecha válido.',
            'metodo_aplicacion.max'     => 'El método de aplicación no puede exceder los 100 caracteres.',
            'estado.in'                 => 'El estado debe ser Pendiente, Realizado o Cancelado.',
            'observaciones.max'         => 'Las observaciones no pueden exceder los 1000 caracteres.',
        ];
    }
}
