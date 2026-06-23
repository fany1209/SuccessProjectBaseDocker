<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
            'name' => 'required|string|max:200',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'rfc' => 'nullable|string|max:15',
            'district' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'address' => 'nullable|string|max:200',
            'sector_id' => 'nullable|integer',
        ];
    }
}
