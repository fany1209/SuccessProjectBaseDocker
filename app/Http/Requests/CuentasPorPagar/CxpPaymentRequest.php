<?php

namespace App\Http\Requests\CuentasPorPagar;

use Illuminate\Foundation\Http\FormRequest;

class CxpPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'notas'       => $this->notas ? strip_tags(trim($this->notas)) : null,
            'banco'       => $this->banco ? strip_tags(trim($this->banco)) : null,
            'metodo_pago' => $this->metodo_pago ? strip_tags(trim($this->metodo_pago)) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'notas'       => 'nullable|string|max:1000',
            'banco'       => 'nullable|string|max:100',
            'metodo_pago' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'   => 'El monto del pago es obligatorio.',
            'amount.numeric'    => 'El monto debe ser un valor numérico.',
            'amount.min'        => 'El monto del pago debe ser mayor a 0.',
            'date.required'     => 'La fecha del pago es obligatoria.',
            'date.date'         => 'La fecha debe tener un formato válido.',
            'comprobante.file'  => 'El comprobante debe ser un archivo.',
            'comprobante.mimes' => 'El comprobante debe ser de tipo: pdf, jpg, jpeg, png o webp.',
            'comprobante.max'   => 'El comprobante no debe superar los 10 MB.',
            'notas.string'      => 'Las notas deben ser texto válido.',
            'notas.max'         => 'Las notas no pueden superar los 1000 caracteres.',
            'banco.string'      => 'El banco debe ser texto válido.',
            'banco.max'         => 'El nombre del banco no puede superar los 100 caracteres.',
            'metodo_pago.string'=> 'El método de pago debe ser texto válido.',
            'metodo_pago.max'   => 'El método de pago no puede superar los 100 caracteres.',
        ];
    }
}
