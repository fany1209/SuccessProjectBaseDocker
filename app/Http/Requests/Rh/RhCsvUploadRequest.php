<?php

namespace App\Http\Requests\Rh;

use Illuminate\Foundation\Http\FormRequest;

class RhCsvUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.required' => 'Debe seleccionar un archivo CSV para importar.',
            'csv_file.file' => 'El archivo adjunto no es válido.',
            'csv_file.mimes' => 'El archivo debe ser de formato CSV o TXT.',
            'csv_file.max' => 'El archivo no debe pesar más de 10 MB.',
        ];
    }
}
