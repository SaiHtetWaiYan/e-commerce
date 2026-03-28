<?php

namespace App\Http\Requests\Vendor;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $product instanceof Product
            ? ($this->user()?->can('update', $product) ?? false)
            : false;
    }

    public function rules(): array
    {
        /** @var Product|null $product */
        $product = $this->route('product');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'short_description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'brand_id' => ['sometimes', 'nullable', 'integer', 'exists:brands,id'],
            'base_price' => ['sometimes', 'numeric', 'min:0.01'],
            'compare_price' => ['sometimes', 'nullable', 'numeric', 'gte:base_price'],
            'sku' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'barcode' => ['sometimes', 'nullable', 'string', 'max:100'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'low_stock_threshold' => ['sometimes', 'integer', 'min:0'],
            'weight' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'length' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'width' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'height' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_digital' => ['sometimes', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'meta_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'meta_description' => ['sometimes', 'nullable', 'string'],
            'category_ids' => ['sometimes', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'base_price.min' => 'Base price must be greater than zero.',
            'compare_price.gte' => 'Compare price must be greater than or equal to base price.',
        ];
    }
}
