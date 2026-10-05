<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
        $productId = $this->input('product_id') ?? $this->route('catalog');
        if (is_object($productId)) {
            $productId = $productId->product_id ?? $productId->id;
        }

        return [
            'product_id'     => 'nullable|integer|exists:products,product_id',
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|integer|exists:categories,category_id',
            'sat_code'       => 'required|string|max:20',
            'sku'            => 'required|string|max:20|unique:products,sku,' . $productId . ',product_id',
            'presentation'   => 'nullable|string|max:100',
            'unit'           => 'nullable|string|max:10',
            'batch_code'     => 'nullable|string|max:20',
            'stock_min'      => 'nullable|numeric|min:0',
            'stock_max'      => 'nullable|numeric|min:0',
            'imgs'           => 'nullable|array',
            'imgs.*'         => 'file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'files'          => 'nullable|array',
            'files.*'        => 'file|mimes:pdf|max:10240',
            'file_sectors'   => 'nullable|array',
            'file_sectors.*' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'El nombre del producto es obligatorio.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists'   => 'La categoría seleccionada no existe.',
            'sat_code.required'    => 'El código SAT es obligatorio.',
            'sku.required'         => 'El código SKU es obligatorio.',
            'sku.unique'           => 'El código SKU ya ha sido registrado.',
            'imgs.*.image'         => 'Cada imagen debe ser un archivo de imagen válido.',
            'imgs.*.mimes'         => 'Las imágenes deben estar en formato: jpeg, png, jpg, gif, webp.',
            'imgs.*.max'           => 'Cada imagen no puede superar los 5MB.',
            'files.*.mimes'        => 'Los archivos deben ser documentos PDF.',
            'files.*.max'          => 'Cada archivo PDF no puede superar los 10MB.',
        ];
    }
}
