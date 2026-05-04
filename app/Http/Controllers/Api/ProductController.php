<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Products
 *
 * Endpoints for managing the product catalogue.
 */
class ProductController extends Controller
{

    /**
     * List products
     *
     * Returns a paginated list of products. Supports full-text search,
     * price-range and stock filters, and column sorting.
     *
     * @queryParam search string Filter by name or description. Example: headphones
     * @queryParam min_price number Filter products with price ≥ this value. Example: 10
     * @queryParam max_price number Filter products with price ≤ this value. Example: 500
     * @queryParam in_stock boolean Return only in-stock (true) or out-of-stock (false) products. Example: true
     * @queryParam sort_by string Column to sort by. Allowed: price, created_at, name. Defaults to created_at. Example: price
     * @queryParam sort_dir string Sort direction: asc or desc. Defaults to desc. Example: asc
     * @queryParam per_page int Number of results per page (1–100). Defaults to 15. Example: 20
     *
     * @response 200 {
     *   "data": [{"id": "uuid", "name": "...", "price": 99.99, "stock": 10, "in_stock": true, "created_at": "..."}],
     *   "links": {"first": "...", "last": "...", "prev": null, "next": "..."},
     *   "meta": {"current_page": 1, "per_page": 15, "total": 100}
     * }
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Product::class);

        $request->validate([
            'search'   => ['nullable', 'string', 'max:100'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'in_stock'  => ['nullable', 'boolean'],
            'sort_by'   => ['nullable', 'string', 'in:price,created_at,name'],
            'sort_dir'  => ['nullable', 'string', 'in:asc,desc'],
            'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $sortBy  = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = (int) $request->input('per_page', 15);

        $products = Product::query()
            ->search($request->input('search'))
            ->priceRange(
                $request->filled('min_price') ? (float) $request->input('min_price') : null,
                $request->filled('max_price') ? (float) $request->input('max_price') : null,
            )
            ->inStock($request->filled('in_stock') ? filter_var($request->input('in_stock'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null)
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * Create a product
     *
     * Creates a new product. Requires authentication.
     *
     * @authenticated
     *
     * @response 201 {"data": {"id": "uuid", "name": "Premium Headphones", "price": 99.99, "stock": 50, "in_stock": true, "created_at": "..."}}
     * @response 422 {"message": "The name field is required.", "errors": {"name": ["The name field is required."]}}
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get a product
     *
     * Returns a single product by its UUID.
     *
     * @urlParam id string required The product UUID. Example: 9c7e5b2a-1234-4abc-8def-000000000001
     *
     * @response 200 {"data": {"id": "uuid", "name": "Premium Headphones", "price": 99.99, "stock": 50, "in_stock": true, "created_at": "..."}}
     * @response 404 {"message": "No query results for model [App\\Models\\Product]."}
     */
    public function show(Product $product): ProductResource
    {
        $this->authorize('view', $product);

        return new ProductResource($product);
    }

    /**
     * Update a product
     *
     * Updates an existing product. Requires authentication.
     * All fields are optional — send only what you want to change.
     *
     * @authenticated
     * @urlParam id string required The product UUID. Example: 9c7e5b2a-1234-4abc-8def-000000000001
     *
     * @response 200 {"data": {"id": "uuid", "name": "Premium Headphones v2", "price": 109.99, "stock": 30, "in_stock": true, "updated_at": "..."}}
     * @response 404 {"message": "No query results for model [App\\Models\\Product]."}
     * @response 422 {"message": "The price must be at least 0.", "errors": {"price": ["The price must be at least 0."]}}
     */
    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());

        return new ProductResource($product->fresh());
    }

    /**
     * Delete a product
     *
     * Soft-deletes a product. The record is retained in the database
     * and can be restored. Requires authentication.
     *
     * @authenticated
     * @urlParam id string required The product UUID. Example: 9c7e5b2a-1234-4abc-8def-000000000001
     *
     * @response 200 {"message": "Product deleted successfully."}
     * @response 404 {"message": "No query results for model [App\\Models\\Product]."}
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
