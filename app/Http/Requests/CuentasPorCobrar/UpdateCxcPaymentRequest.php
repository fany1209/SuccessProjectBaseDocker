<?php

namespace App\Http\Requests\CuentasPorCobrar;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCxcPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => is_string($this->amount) ? trim($this->amount) : $this->amount,
            ]);
        }
        if ($this->has('date')) {
            $this->merge([
                'date' => is_string($this->date) ? trim($this->date) : $this->date,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'date'        => ['required', 'date'],
            'comprobante' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:25600'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'   => 'El monto del abono es obligatorio.',
            'amount.numeric'    => 'El monto del abono debe ser un valor numérico.',
            'amount.min'        => 'El monto del abono debe ser mayor a cero.',
            'date.required'     => 'La fecha del abono es obligatoria.',
            'date.date'         => 'La fecha del abono debe tener un formato válido.',
            'comprobante.file'  => 'El comprobante debe ser un archivo válido.',
            'comprobante.mimes' => 'El comprobante debe ser en formato JPG, PNG o PDF.',
            'comprobante.max'   => 'El comprobante no debe exceder los 25 MB.',
        ];
    }
}
