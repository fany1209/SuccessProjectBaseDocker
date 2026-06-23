<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'sku' => 'required|string|max:20', 
            'sat_code' => 'required|string|max:20',
            'presentation' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:10',
            'batch_code' => 'nullable|string|max:20',
            'stock_min' => 'nullable|numeric|min:0',
            'stock_max' => 'nullable|numeric|min:0',
            'imgs.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'files.*' => 'file|mimes:pdf|max:2048',
        ];
    }
}
