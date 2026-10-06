<?php

namespace App\Http\Requests\RecursosHumanos;

use Illuminate\Foundation\Http\FormRequest;

class CursoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $asistentes = $this->input('asistentes');
        if (is_array($asistentes)) {
            $asistentes = array_values(array_filter(array_map(function ($item) {
                return is_string($item) ? strip_tags(trim($item)) : $item;
            }, $asistentes), function ($value) {
                return !is_null($value) && $value !== '';
            }));
        }

        $this->merge([
            'fecha' => $this->filled('fecha') ? strip_tags(trim($this->input('fecha'))) : null,
            'sede' => $this->filled('sede') ? strip_tags(trim($this->input('sede'))) : null,
            'horario' => $this->filled('horario') ? strip_tags(trim($this->input('horario'))) : null,
            'curso' => $this->filled('curso') ? strip_tags(trim($this->input('curso'))) : null,
            'objetivo' => $this->filled('objetivo') ? strip_tags(trim($this->input('objetivo'))) : null,
            'asistentes' => $asistentes,
        ]);
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date'],
            'sede' => ['required', 'string', 'max:255'],
            'horario' => ['required', 'string', 'max:255'],
            'curso' => ['required', 'string', 'max:255'],
            'objetivo' => ['required', 'string'],
            'asistentes' => ['nullable', 'array'],
            'asistentes.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha del curso es obligatoria.',
            'fecha.date' => 'La fecha del curso debe ser una fecha válida.',
            'sede.required' => 'La sede del curso es obligatoria.',
            'sede.string' => 'La sede debe ser una cadena de texto.',
            'sede.max' => 'La sede no puede exceder 255 caracteres.',
            'horario.required' => 'El horario del curso es obligatorio.',
            'horario.string' => 'El horario debe ser una cadena de texto.',
            'horario.max' => 'El horario no puede exceder 255 caracteres.',
            'curso.required' => 'El nombre del curso es obligatorio.',
            'curso.string' => 'El nombre del curso debe ser una cadena de texto.',
            'curso.max' => 'El nombre del curso no puede exceder 255 caracteres.',
            'objetivo.required' => 'El objetivo del curso es obligatorio.',
            'objetivo.string' => 'El objetivo debe ser una cadena de texto.',
            'asistentes.array' => 'La lista de asistentes debe ser un arreglo.',
            'asistentes.*.string' => 'El nombre de cada asistente debe ser una cadena de texto.',
            'asistentes.*.max' => 'El nombre del asistente no debe exceder 255 caracteres.',
        ];
    }
}
