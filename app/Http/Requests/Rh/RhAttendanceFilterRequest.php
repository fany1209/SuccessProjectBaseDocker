<?php

namespace App\Http\Requests\Rh;

use Illuminate\Foundation\Http\FormRequest;

class RhAttendanceFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'empleado' => $this->filled('empleado') ? strip_tags(trim($this->input('empleado'))) : null,
            'fecha_inicio' => $this->filled('fecha_inicio') ? strip_tags(trim($this->input('fecha_inicio'))) : null,
            'fecha_fin' => $this->filled('fecha_fin') ? strip_tags(trim($this->input('fecha_fin'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'empleado' => ['nullable', 'string', 'max:255'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ];
    }

    public function messages(): array
    {
        return [
            'empleado.string' => 'El nombre del empleado debe ser una cadena de texto válida.',
            'empleado.max' => 'El nombre del empleado no debe exceder 255 caracteres.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        ];
    }
}
