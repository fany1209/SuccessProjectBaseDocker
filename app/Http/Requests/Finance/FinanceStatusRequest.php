<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class FinanceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('estatus') && is_string($this->estatus)) {
            $this->merge([
                'estatus' => strtoupper(strip_tags(trim($this->estatus))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'estatus' => ['required', 'string', 'in:PENDIENTE,PAGADO,CANCELADO'],
        ];
    }

    public function messages(): array
    {
        return [
            'estatus.required' => 'El estatus es obligatorio.',
            'estatus.in'       => 'El estatus debe ser PENDIENTE, PAGADO o CANCELADO.',
        ];
    }
}
