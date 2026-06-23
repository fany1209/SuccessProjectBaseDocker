<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProspectRequest extends FormRequest
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
            'prospect_id' => 'nullable|exists:prospects,prospect_id', 
            'sector_id'   => 'required',
            'name'        => 'required|max:200',
            'phone'    => 'nullable|max:20',
            'email'    => 'nullable|email|max:150',
            'rfc'      => 'nullable|max:13',
            'state'    => 'nullable|max:80',
            'city'     => 'nullable|max:80',
            'district' => 'nullable|max:80',
            'address'  => 'nullable|max:200',
        ];
    }
}
