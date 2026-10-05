<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

class QuoteStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quotes_status_id' => 'required|integer|exists:quotes_status,quotes_status_id',
        ];
    }

    public function messages(): array
    {
        return [
            'quotes_status_id.required' => 'El identificador del estatus es obligatorio.',
            'quotes_status_id.exists'   => 'El estatus seleccionado no existe.',
        ];
    }
}
