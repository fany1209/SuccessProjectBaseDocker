<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

class QuoteStoreRequest extends FormRequest
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
            } elseif (is_array($value) && $key === 'products') {
                $sanitized['products'] = array_map(function ($item) {
                    if (is_array($item)) {
                        foreach ($item as $subKey => $subVal) {
                            if (is_string($subVal)) {
                                $item[$subKey] = strip_tags(trim($subVal));
                            }
                        }
                    }
                    return $item;
                }, $value);
            }
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'company'                 => 'required|string|max:150',
            'date'                    => 'required|date',
            'currency'                => 'nullable|string|in:MXN,USD',
            'quotes_status_id'        => 'required|integer|exists:quotes_status,quotes_status_id',
            'attention'               => 'nullable|string|max:150',
            'department'              => 'nullable|string|max:100',
            'phone'                   => 'nullable|string|max:30',
            'place_of_delivery'       => 'nullable|string|max:200',
            'transport_specification' => 'nullable|string|max:200',
            'deadline'                => 'nullable|string|max:100',
            'terms'                   => 'nullable|string|max:200',
            'notes'                   => 'nullable|string|max:1000',
            'products'                => 'required|array|min:1',
            'products.*.product_id'   => 'required|integer|exists:products,product_id',
            'products.*.quote_product_name' => 'nullable|string|max:255',
            'products.*.quantity'     => 'required|numeric|min:0',
            'products.*.cost'         => 'required|numeric|min:0',
            'products.*.presentation' => 'nullable|string|max:100',
            'products.*.unit'         => 'nullable|string|max:20',
            'products.*.iva'          => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'company.required'          => 'La empresa o cliente es obligatorio.',
            'date.required'             => 'La fecha es obligatoria.',
            'date.date'                 => 'La fecha debe tener un formato válido.',
            'quotes_status_id.required' => 'El estatus de la cotización es obligatorio.',
            'quotes_status_id.exists'   => 'El estatus seleccionado no existe.',
            'products.required'         => 'Debe incluir al menos un producto en la cotización.',
            'products.min'              => 'Debe incluir al menos un producto en la cotización.',
            'products.*.quantity.required' => 'La cantidad del producto es obligatoria.',
            'products.*.cost.required'     => 'El costo del producto es obligatorio.',
        ];
    }
}
