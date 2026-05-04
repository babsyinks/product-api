<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure a clean slate in dev/test
        Product::withTrashed()->forceDelete();

        // 75 standard products (mix of in-stock and out-of-stock)
        Product::factory(60)->inStock()->create();
        Product::factory(15)->outOfStock()->create();

        // 10 budget products (price < $20)
        Product::factory(10)->priceRange(0.99, 19.99)->inStock(5)->create();

        // 10 premium products (price > $500)
        Product::factory(10)->priceRange(500, 9999.99)->inStock()->create();

        // 5 soft-deleted products (verifies exclude from default queries)
        Product::factory(5)->deleted()->create();

        $total = Product::withTrashed()->count();

       $active = $total - 5;
       $this->command->info("Seeded {$total} products ({$active} active, 5 soft-deleted).");
    }
}
