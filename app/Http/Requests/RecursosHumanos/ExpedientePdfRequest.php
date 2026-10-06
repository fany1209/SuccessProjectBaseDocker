<?php

namespace App\Http\Requests\RecursosHumanos;

use Illuminate\Foundation\Http\FormRequest;

class ExpedientePdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) {
            $this->merge([
                'nombre' => strip_tags(trim((string)$this->input('nombre'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'foto'   => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del empleado es obligatorio.',
            'nombre.string'   => 'El nombre debe ser una cadena de texto.',
            'nombre.max'      => 'El nombre no puede exceder 255 caracteres.',
            'foto.image'      => 'El archivo de la fotografía debe ser una imagen válida.',
            'foto.mimes'      => 'La fotografía debe estar en formato JPEG, JPG o PNG.',
            'foto.max'        => 'La fotografía no debe superar los 2 MB.',
        ];
    }
}
