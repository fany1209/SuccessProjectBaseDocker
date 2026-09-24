<?php

namespace App\Http\Requests\Contrato;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'mes_1' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'mes_2' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'mes_3' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'indefinido' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'confidencialidad' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El trabajador es obligatorio.',
            'user_id.integer' => 'El ID del trabajador debe ser un número entero.',
            'user_id.exists' => 'El trabajador seleccionado no existe.',
            'mes_1.file' => 'El archivo de Mes 1 debe ser un archivo válido.',
            'mes_1.mimes' => 'El contrato de Mes 1 debe ser en formato PDF.',
            'mes_1.max' => 'El contrato de Mes 1 no debe pesar más de 10 MB.',
            'mes_2.file' => 'El archivo de Mes 2 debe ser un archivo válido.',
            'mes_2.mimes' => 'El contrato de Mes 2 debe ser en formato PDF.',
            'mes_2.max' => 'El contrato de Mes 2 no debe pesar más de 10 MB.',
            'mes_3.file' => 'El archivo de Mes 3 debe ser un archivo válido.',
            'mes_3.mimes' => 'El contrato de Mes 3 debe ser en formato PDF.',
            'mes_3.max' => 'El contrato de Mes 3 no debe pesar más de 10 MB.',
            'indefinido.file' => 'El archivo de contrato Indefinido debe ser un archivo válido.',
            'indefinido.mimes' => 'El contrato Indefinido debe ser en formato PDF.',
            'indefinido.max' => 'El contrato Indefinido no debe pesar más de 10 MB.',
            'confidencialidad.file' => 'El archivo de Confidencialidad debe ser un archivo válido.',
            'confidencialidad.mimes' => 'El acuerdo de Confidencialidad debe ser en formato PDF.',
            'confidencialidad.max' => 'El acuerdo de Confidencialidad no debe pesar más de 10 MB.',
        ];
    }
}
