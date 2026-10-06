<?php

namespace App\Http\Requests\Nomina;

use Illuminate\Foundation\Http\FormRequest;

class NominaUpdateRequest extends FormRequest
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
                $trimmed = strip_tags(trim($value));
                if (in_array($key, ['curp', 'rfc'], true)) {
                    $trimmed = strtoupper($trimmed);
                }
                $sanitized[$key] = $trimmed;
            } elseif (is_array($value)) {
                $sanitized[$key] = array_map(function ($item) {
                    return is_string($item) ? strip_tags(trim($item)) : $item;
                }, $value);
            }
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'nombre'              => 'required|string|max:255',
            'curp'                => 'nullable|string|max:18',
            'rfc'                 => 'nullable|string|max:13',
            'nss'                 => 'nullable|string|max:20',
            'puesto'              => 'required|string|max:255',
            'fecha_ingreso'       => 'required|date',
            'fecha_baja'          => 'nullable|date',
            'sexo'                => 'nullable|string|max:20',
            'estado_civil'        => 'nullable|string|max:50',
            'fecha_nacimiento'    => 'nullable|date',
            'nombre_beneficiario' => 'nullable|string|max:255',
            'parentesco'          => 'nullable|string|max:100',
            'domicilio'           => 'nullable|string|max:500',
            'cp'                  => 'nullable|string|max:10',
            'telefono'            => 'nullable|string|max:30',
            'correo'              => 'nullable|email|max:150',
            'estatus'             => 'nullable|string|in:Activo,Baja',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'        => 'El nombre del empleado es obligatorio.',
            'puesto.required'        => 'El puesto es obligatorio.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'fecha_ingreso.date'     => 'La fecha de ingreso debe ser una fecha válida.',
            'correo.email'           => 'El correo debe tener un formato válido.',
            'estatus.in'             => 'El estatus debe ser Activo o Baja.',
        ];
    }
}
