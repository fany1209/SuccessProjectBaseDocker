<?php

namespace App\Http\Requests\Fertil;

use Illuminate\Foundation\Http\FormRequest;

class FertilMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $products = $this->products;
        if (is_array($products)) {
            foreach ($products as $k => $prod) {
                if (is_array($prod) && isset($prod['name']) && is_string($prod['name'])) {
                    $products[$k]['name'] = strip_tags(trim($prod['name']));
                }
            }
        }

        $this->merge([
            'applicant_name' => is_string($this->applicant_name) ? strip_tags(trim($this->applicant_name)) : $this->applicant_name,
            'comments'       => is_string($this->comments) ? strip_tags(trim($this->comments)) : $this->comments,
            'products'       => $products,
        ]);
    }

    public function rules(): array
    {
        return [
            'applicant_name'      => ['required', 'string', 'max:100'],
            'comments'            => ['nullable', 'string', 'max:1000'],
            'products'            => ['required', 'array', 'min:1'],
            'products.*.name'     => ['required', 'string', 'max:255'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_name.required'      => 'El nombre del solicitante es obligatorio.',
            'applicant_name.max'           => 'El nombre del solicitante no puede exceder los 100 caracteres.',
            'comments.max'                 => 'Los comentarios no pueden exceder los 1000 caracteres.',
            'products.required'            => 'Debe incluir al menos un producto en la solicitud.',
            'products.array'               => 'La lista de productos tiene un formato inválido.',
            'products.min'                 => 'Debe incluir al menos un producto en la solicitud.',
            'products.*.name.required'     => 'El nombre del producto es obligatorio.',
            'products.*.quantity.required' => 'La cantidad del producto es obligatoria.',
            'products.*.quantity.min'      => 'La cantidad solicitada debe ser mayor a 0.',
        ];
    }
}
