<?php

namespace App\Http\Requests\CuentasPorPagar;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCxpDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'banco'        => $this->banco ? strip_tags(trim($this->banco)) : null,
            'comentarios'  => $this->comentarios ? strip_tags(trim($this->comentarios)) : null,
            'departamento' => $this->departamento ? strip_tags(trim($this->departamento)) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'semana'         => 'nullable|integer|min:1|max:53',
            'banco'          => 'nullable|string|max:100',
            'comentarios'    => 'nullable|string|max:2000',
            'comentario_img' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:20480',
            'departamento'   => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'semana.integer'         => 'La semana debe ser un número entero válido.',
            'semana.min'             => 'La semana debe ser mayor o igual a 1.',
            'semana.max'             => 'La semana no puede ser mayor a 53.',
            'banco.string'           => 'El nombre del banco debe ser un texto válido.',
            'banco.max'              => 'El nombre del banco no puede exceder los 100 caracteres.',
            'comentarios.string'     => 'Los comentarios deben ser texto válido.',
            'comentarios.max'        => 'Los comentarios no pueden exceder los 2000 caracteres.',
            'comentario_img.file'    => 'El archivo adjunto debe ser un archivo válido.',
            'comentario_img.mimes'   => 'El archivo debe tener un formato válido: jpeg, png, jpg, webp o pdf.',
            'comentario_img.max'     => 'El archivo no puede pesar más de 20 MB.',
            'departamento.string'    => 'El departamento debe ser un texto válido.',
            'departamento.max'       => 'El departamento no puede exceder los 255 caracteres.',
        ];
    }
}
