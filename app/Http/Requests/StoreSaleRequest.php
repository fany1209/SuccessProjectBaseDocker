<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
  public function rules(): array
    {
        return [
            'seller' => 'required|string|max:150',
            'first_time' => 'nullable|integer',
            'is_customer' => 'required|integer',
            'purchase_order' => 'nullable|string|max:150',
            'invoice' => 'nullable|string|max:150',
            'sale_type' => 'required|string|max:30',
            'term' => 'nullable|string|max:100',
            'date' => 'required|date',
            'folio' => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'prospect_id' => 'nullable|integer',
            'user_id' => 'required|integer',
            'sales_status_id' => 'required|integer',
            'sector_id' => 'required|integer',
            'product_id'   => 'required|array|min:1', 
            'product_id.*' => 'required|integer|exists:products,product_id',
            'quantity'     => 'required|array',
            'quantity.*'   => 'required|numeric|min:0.001',
            'cost'         => 'required|array',
            'cost.*'       => 'required|numeric|min:0',
            'has_tax'      => 'nullable|array',
            'has_tax.*'    => 'nullable|integer',
            'invoice_val'            => 'nullable|array',
            'invoice_val.*'          => 'nullable|integer|in:0,1',
            'public_product_name'    => 'nullable|array',
            'public_product_name.*'  => 'nullable|string|max:200',
            'public_batch'           => 'nullable|array',
            'public_batch.*'         => 'nullable|string|max:100',
        ];
    }
}
