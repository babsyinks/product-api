<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam name string The product name. Max 255 characters. Example: Premium Headphones v2
 * @bodyParam description string nullable A detailed product description. Example: Updated noise-cancelling model.
 * @bodyParam price number The product price (must be ≥ 0). Example: 109.99
 * @bodyParam stock integer Current stock level (must be ≥ 0). Example: 30
 */
class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('product'));
    }

    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['sometimes', 'required', 'numeric', 'min:0', 'decimal:0,2'],
            'stock'       => ['sometimes', 'required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.decimal' => 'The price must have at most 2 decimal places.',
        ];
    }
}
