<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusUpdateRequest extends FormRequest
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
            }
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'estatus_almacen'        => 'nullable|string|max:50',
            'estatus_calidad'        => 'nullable|string|max:50',
            'estatus_administrativo' => 'nullable|string|max:50',
        ];
    }
}
