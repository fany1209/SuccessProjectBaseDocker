<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class SaleStoreRequest extends FormRequest
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
                $sanitized[$key] = array_map(function ($item) {
                    if (is_string($item)) {
                        return strip_tags(trim($item));
                    }
                    if (is_array($item)) {
                        return array_map(function ($sub) {
                            return is_string($sub) ? strip_tags(trim($sub)) : $sub;
                        }, $item);
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
            'seller'                => 'required|string|max:150',
            'first_time'            => 'required|integer|in:0,1',
            'is_customer'           => 'required|integer|in:0,1',
            'purchase_order'        => 'nullable|string|max:150',
            'invoice'               => 'nullable|string|max:150',
            'sale_type'             => 'required|string|max:30',
            'term'                  => 'nullable|string|max:100',
            'date'                  => 'required|date',
            'folio'                 => 'nullable|integer',
            'customer_id'           => 'nullable|integer|exists:customers,customer_id',
            'prospect_id'           => 'nullable|integer|exists:prospects,prospect_id',
            'user_id'               => 'required|integer|exists:users,id',
            'sales_status_id'       => 'required|integer|exists:sales_status,sales_status_id',
            'sector_id'             => 'required|integer|exists:sectors,sector_id',
            'name'                  => 'required_if:first_time,0,is_customer,0|nullable|string|max:150',
            'phone'                 => 'nullable|string|max:30',
            'email'                 => 'nullable|email|max:150',
            'rfc'                   => 'nullable|string|max:30',
            'state'                 => 'nullable|string|max:100',
            'city'                  => 'nullable|string|max:100',
            'district'              => 'nullable|string|max:100',
            'address'               => 'nullable|string|max:200',
            'product_id'            => 'required|array|min:1',
            'product_id.*'          => 'required|integer|exists:products,product_id',
            'quantity'              => 'required|array|min:1',
            'quantity.*'            => 'required|numeric|min:0.001',
            'cost'                  => 'required|array|min:1',
            'cost.*'                => 'required|numeric|min:0',
            'has_tax'               => 'nullable|array',
            'has_tax.*'             => 'nullable|integer|in:0,1',
            'invoice_val'           => 'nullable|array',
            'invoice_val.*'         => 'nullable|integer|in:0,1',
            'public_product_name'   => 'nullable|array',
            'public_product_name.*' => 'nullable|string|max:200',
            'public_batch'          => 'nullable|array',
            'public_batch.*'        => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'seller.required'          => 'El vendedor es obligatorio.',
            'sale_type.required'       => 'El tipo de venta es obligatorio.',
            'date.required'            => 'La fecha de la venta es obligatoria.',
            'sales_status_id.required' => 'El estatus de la venta es obligatorio.',
            'sector_id.required'       => 'El sector es obligatorio.',
            'product_id.required'      => 'Debe incluir al menos un producto en la venta.',
            'product_id.min'           => 'Debe incluir al menos un producto en la venta.',
            'quantity.required'        => 'Las cantidades de los productos son obligatorias.',
            'cost.required'            => 'Los costos de los productos son obligatorios.',
            'name.required_if'         => 'El nombre del cliente es obligatorio para registros manuales.',
        ];
    }
}
