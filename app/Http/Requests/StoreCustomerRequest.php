<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
    public function rules()
    {
        return [
            'sector_id' => 'required|exists:sectors,sector_id',
            'name'      => 'required|string|max:200',
            'phone'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:150',
            'rfc'              => 'nullable|string|max:13',
            'postal_code'      => 'nullable|string|max:10',
            'state'            => 'nullable|string|max:80',
            'city'             => 'nullable|string|max:80',
            'district'         => 'nullable|string|max:80',
            'address'          => 'nullable|string|max:200',
            'country'          => 'nullable|string|max:100',
            'contact'          => 'nullable|string|max:150',
            'delivery_address' => 'nullable|string',
        ];
    }
}
