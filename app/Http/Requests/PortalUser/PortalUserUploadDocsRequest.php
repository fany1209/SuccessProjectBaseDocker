<?php

namespace App\Http\Requests\PortalUser;

use Illuminate\Foundation\Http\FormRequest;

class PortalUserUploadDocsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'xml_file' => ['nullable', 'file', 'mimes:xml,txt', 'max:5120'],
            'coa_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'pdf_file.file'  => 'El archivo PDF debe ser un archivo válido.',
            'pdf_file.mimes' => 'El archivo de factura debe tener formato PDF.',
            'pdf_file.max'   => 'El archivo PDF no puede exceder 10MB.',
            'xml_file.file'  => 'El archivo XML debe ser un archivo válido.',
            'xml_file.mimes' => 'El archivo XML debe tener formato XML o TXT.',
            'xml_file.max'   => 'El archivo XML no puede exceder 5MB.',
            'coa_file.file'  => 'El archivo COA debe ser un archivo válido.',
            'coa_file.mimes' => 'El certificado COA debe tener formato PDF.',
            'coa_file.max'   => 'El certificado COA no puede exceder 10MB.',
        ];
    }
}
