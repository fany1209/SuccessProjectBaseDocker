<?php

namespace App\Http\Requests\CuentasPorPagar;

use Illuminate\Foundation\Http\FormRequest;

class UploadCxpDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pdf'      => 'nullable|file|mimes:pdf|max:10240',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'xml'      => 'nullable|file|mimes:xml,text/xml|max:10240',
            'xml_file' => 'nullable|file|mimes:xml,text/xml|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'pdf.file'  => 'El archivo PDF debe ser un archivo válido.',
            'pdf.mimes' => 'El archivo PDF debe tener extensión .pdf.',
            'pdf.max'   => 'El archivo PDF no puede exceder los 10 MB.',
            'xml.file'  => 'El archivo XML debe ser un archivo válido.',
            'xml.mimes' => 'El archivo XML debe tener formato XML válido.',
            'xml.max'   => 'El archivo XML no puede exceder los 10 MB.',
        ];
    }
}
