<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    private static array $categories = [
        'Electronics', 'Books', 'Clothing', 'Home & Garden',
        'Sports', 'Toys', 'Automotive', 'Health & Beauty',
        'Food & Grocery', 'Office Supplies',
    ];

    public function definition(): array
    {
        $category = $this->faker->randomElement(self::$categories);

        return [
            'name'        => $this->generateProductName($category),
            'description' => $this->faker->paragraphs(
                nb: $this->faker->numberBetween(1, 3),
                asText: true
            ),
            'price'  => $this->faker->randomFloat(2, 0.99, 9999.99),
            'stock'  => $this->faker->numberBetween(0, 500),
        ];
    }

    /**
     * Generate a realistic product name for the given category.
     */
    private function generateProductName(string $category): string
    {
        $adjective = $this->faker->randomElement([
            'Premium', 'Pro', 'Ultra', 'Deluxe', 'Classic',
            'Essential', 'Advanced', 'Compact', 'Heavy-Duty', 'Wireless',
        ]);

        $noun = match ($category) {
            'Electronics'    => $this->faker->randomElement(['Headphones', 'Speaker', 'Charger', 'Keyboard', 'Mouse', 'Monitor']),
            'Books'          => $this->faker->randomElement(['Guide', 'Handbook', 'Manual', 'Encyclopedia', 'Workbook']),
            'Clothing'       => $this->faker->randomElement(['Jacket', 'Shirt', 'Trousers', 'Hoodie', 'Sneakers']),
            'Home & Garden'  => $this->faker->randomElement(['Lamp', 'Planter', 'Cushion', 'Rug', 'Blender']),
            'Sports'         => $this->faker->randomElement(['Dumbbell', 'Yoga Mat', 'Water Bottle', 'Resistance Band', 'Gloves']),
            'Toys'           => $this->faker->randomElement(['Building Blocks', 'Puzzle', 'Action Figure', 'Board Game', 'Doll']),
            'Automotive'     => $this->faker->randomElement(['Dash Cam', 'Car Mat', 'Phone Mount', 'Jump Starter', 'Air Freshener']),
            'Health & Beauty' => $this->faker->randomElement(['Moisturiser', 'Serum', 'Vitamin Pack', 'Face Mask', 'Toothbrush']),
            'Food & Grocery' => $this->faker->randomElement(['Protein Powder', 'Coffee Blend', 'Olive Oil', 'Spice Mix', 'Tea Set']),
            default          => $this->faker->randomElement(['Tool', 'Accessory', 'Kit', 'Set', 'Pack']),
        };

        return "{$adjective} {$noun}";
    }

    // States

    /**
     * Mark the product as out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }

    /**
     * Mark the product as in stock.
     */
    public function inStock(int $min = 1): static
    {
        return $this->state(['stock' => $this->faker->numberBetween($min, 500)]);
    }

    /**
     * Set a fixed price range.
     */
    public function priceRange(float $min, float $max): static
    {
        return $this->state([
            'price' => $this->faker->randomFloat(2, $min, $max),
        ]);
    }

    /**
     * Soft-delete the product.
     */
    public function deleted(): static
    {
        return $this->state(['deleted_at' => now()]);
    }
}
