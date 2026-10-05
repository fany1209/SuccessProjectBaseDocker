<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class SaleAlmacenActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = strip_tags(trim($value));
            } elseif (is_array($value)) {
                $sanitized[$key] = $value;
            }
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'action'                          => 'required|string|in:confirm,postpone,mark_ready,cancel',
            'lot_assignments'                 => 'required_if:action,confirm|array',
            'lot_assignments.*.cli_id'        => 'required_if:action,confirm|integer',
            'lot_assignments.*.quantity'      => 'required_if:action,confirm|numeric|min:0.001',
            'reason'                          => 'required_if:action,postpone,cancel|nullable|string|max:1000',
            'date'                            => 'required_if:action,postpone|nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'action.required'                 => 'La acción de almacén es requerida.',
            'action.in'                       => 'La acción indicada no es válida.',
            'lot_assignments.required_if'     => 'Debes seleccionar un lote para cada producto.',
            'reason.required_if'              => 'El motivo es obligatorio para esta acción.',
            'date.required_if'                => 'La fecha es obligatoria para posponer la venta.',
        ];
    }
}
