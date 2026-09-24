<?php

namespace App\Http\Requests\CuentasPorCobrar;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCxcDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'documento' => is_string($this->documento) ? strip_tags(trim($this->documento)) : $this->documento,
            'metodo_pago' => is_string($this->metodo_pago) ? strip_tags(trim($this->metodo_pago)) : $this->metodo_pago,
            'descripcion' => is_string($this->descripcion) ? strip_tags(trim($this->descripcion)) : $this->descripcion,
        ]);
    }

    public function rules(): array
    {
        return [
            'documento' => ['nullable', 'string', 'max:50'],
            'metodo_pago' => ['required', 'in:N/A,PUE,PPD'],
            'fecha_conclusion' => ['nullable', 'date'],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'documento.max' => 'El documento no puede exceder los 50 caracteres.',
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'metodo_pago.in' => 'El método de pago seleccionado no es válido (debe ser N/A, PUE o PPD).',
            'fecha_conclusion.date' => 'La fecha de conclusión no tiene un formato válido.',
        ];
    }
}
