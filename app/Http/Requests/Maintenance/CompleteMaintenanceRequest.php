<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class CompleteMaintenanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'evidence_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'evidence_file.required' => 'El archivo de evidencia es obligatorio para completar el mantenimiento.',
            'evidence_file.file'     => 'El archivo de evidencia debe ser un archivo válido.',
            'evidence_file.mimes'    => 'El archivo de evidencia debe tener formato PDF, JPG, JPEG o PNG.',
            'evidence_file.max'      => 'El archivo de evidencia no debe superar los 5 MB.',
        ];
    }
}
