<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam name string required The product name. Max 255 characters. Example: Premium Headphones
 * @bodyParam description string nullable A detailed product description. Example: High-fidelity over-ear headphones with noise cancellation.
 * @bodyParam price number required The product price (must be ≥ 0). Example: 99.99
 * @bodyParam stock integer required Current stock level (must be ≥ 0). Example: 50
 */
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Product::class);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'stock'       => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.decimal' => 'The price must have at most 2 decimal places.',
        ];
    }
}
