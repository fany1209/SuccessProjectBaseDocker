<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequisitionRequest extends FormRequest
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
            'applicant' => 'required|string|max:150',
            'department' => 'required|string|max:70',
            'data_sheet' => 'required|integer',
            'safety_sheet' => 'required|integer',
            'description' => 'array',
            'description.*' => 'required|string|max:1000',
            'url' => 'array',
            'url.*' => 'required|string|max:1024',
            'use' => 'array',
            'use.*' => 'required|string|max:100',
            'quantity' => 'array',
            'quantity.*' => 'required|integer',
            'unit' => 'array',
            'unit.*' => 'required|string|max:5',
            'image_url' => 'array',
            'image_url.*' => 'required|string',
        ];
    }
}
