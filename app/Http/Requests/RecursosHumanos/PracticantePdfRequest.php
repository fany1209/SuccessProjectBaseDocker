<?php

namespace App\Http\Requests\RecursosHumanos;

use Illuminate\Foundation\Http\FormRequest;

class PracticantePdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto_infantil' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:3072'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto_infantil.file'  => 'El archivo de la fotografía infantil no es válido.',
            'foto_infantil.image' => 'El archivo adjunto debe ser una imagen.',
            'foto_infantil.mimes' => 'La imagen debe ser de formato JPEG, JPG, PNG o WEBP.',
            'foto_infantil.max'   => 'La fotografía no debe exceder 3 MB.',
        ];
    }
}
