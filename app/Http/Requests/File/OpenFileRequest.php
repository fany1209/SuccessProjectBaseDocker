<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class OpenFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fileName = $this->route('fileName') ?? $this->fileName;

        if (is_string($fileName)) {
            $sanitized = str_replace(["\0", "\r", "\n"], '', urldecode($fileName));
            $cleanFileName = basename(str_replace('\\', '/', $sanitized));

            $this->merge([
                'fileName' => $cleanFileName,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'fileName' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_\-\.\s]+\.(pdf|doc|docx|xls|xlsx|csv|txt)$/i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fileName.required' => 'El nombre del archivo es obligatorio.',
            'fileName.string'   => 'El nombre del archivo debe ser una cadena de texto.',
            'fileName.max'      => 'El nombre del archivo no puede exceder los 255 caracteres.',
            'fileName.regex'    => 'El tipo de archivo o formato no es válido. Solo se permiten extensiones: pdf, doc, docx, xls, xlsx, csv, txt.',
        ];
    }
}
