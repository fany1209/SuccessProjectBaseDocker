<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class SaleStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sales_status_id' => 'required|integer|exists:sales_status,sales_status_id',
        ];
    }

    public function messages(): array
    {
        return [
            'sales_status_id.required' => 'El estatus de venta es obligatorio.',
            'sales_status_id.exists'   => 'El estatus seleccionado no existe.',
        ];
    }
}
