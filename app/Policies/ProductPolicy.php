<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Anyone (including guests) may list products.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Anyone (including guests) may view a single product.
     */
    public function view(?User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Only authenticated users may create products.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only authenticated users may update products.
     *
     * Extend this to restrict to the product owner:
     *   return $user->id === $product->user_id;
     */
    public function update(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Only authenticated users may delete products.
     */
    public function delete(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Only authenticated users may restore soft-deleted products.
     */
    public function restore(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Only authenticated users may permanently delete products.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return true;
    }
}
