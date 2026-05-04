<?php

use App\Models\Product;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// Guest access

test('guests can list products', function () {
    Product::factory(5)->create();

    $response = $this->getJson('/api/v1/products');

    $response->assertOk()
             ->assertJsonStructure([
                 'data' => [['id', 'name', 'price', 'stock', 'in_stock', 'created_at', 'updated_at']],
                 'links',
                 'meta',
             ]);
});

test('guests can view a single product', function () {
    $product = Product::factory()->create();

    $this->getJson("/api/v1/products/{$product->id}")
         ->assertOk()
         ->assertJsonPath('data.id', $product->id)
         ->assertJsonPath('data.name', $product->name);
});

test('guests cannot create a product', function () {
    $this->postJson('/api/v1/products', [
        'name'  => 'Unauthorized',
        'price' => 10.00,
        'stock' => 5,
    ])->assertUnauthorized();
});

test('guests cannot update a product', function () {
    $product = Product::factory()->create();

    $this->putJson("/api/v1/products/{$product->id}", ['name' => 'Hacked'])
         ->assertUnauthorized();
});

test('guests cannot delete a product', function () {
    $product = Product::factory()->create();

    $this->deleteJson("/api/v1/products/{$product->id}")
         ->assertUnauthorized();
});

// Authenticated access

test('authenticated user can create a product', function () {
    $user = User::factory()->create();

    $payload = [
        'name'        => 'Test Product',
        'description' => 'A test product',
        'price'       => 29.99,
        'stock'       => 100,
    ];

    $this->actingAs($user)
         ->postJson('/api/v1/products', $payload)
         ->assertCreated()
         ->assertJsonPath('data.name', 'Test Product')
         ->assertJsonPath('data.price', 29.99)
         ->assertJsonPath('data.stock', 100);

    $this->assertDatabaseHas('products', ['name' => 'Test Product']);
});

test('authenticated user can update a product', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['price' => 10.00]);

    $this->actingAs($user)
         ->putJson("/api/v1/products/{$product->id}", ['price' => 25.00])
         ->assertOk()
         ->assertJsonPath('data.price', 25.00);
});

test('authenticated user can soft-delete a product', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($user)
         ->deleteJson("/api/v1/products/{$product->id}")
         ->assertOk()
         ->assertJsonPath('message', 'Product deleted successfully.');

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

// Validation

test('store validates required fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
         ->postJson('/api/v1/products', [])
         ->assertUnprocessable()
         ->assertJsonValidationErrors(['name', 'price', 'stock']);
});

test('store rejects negative price', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
         ->postJson('/api/v1/products', ['name' => 'X', 'price' => -1, 'stock' => 0])
         ->assertUnprocessable()
         ->assertJsonValidationErrors(['price']);
});

test('store rejects negative stock', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
         ->postJson('/api/v1/products', ['name' => 'X', 'price' => 1, 'stock' => -1])
         ->assertUnprocessable()
         ->assertJsonValidationErrors(['stock']);
});

// Filters & search

test('can search products by name', function () {
    Product::factory()->create(['name' => 'Wireless Mouse']);
    Product::factory()->create(['name' => 'Mechanical Keyboard']);

    $this->getJson('/api/v1/products?search=wireless')
         ->assertOk()
         ->assertJsonCount(1, 'data')
         ->assertJsonPath('data.0.name', 'Wireless Mouse');
});

test('can filter by price range', function () {
    Product::factory()->create(['price' => 5.00]);
    Product::factory()->create(['price' => 50.00]);
    Product::factory()->create(['price' => 500.00]);

    $this->getJson('/api/v1/products?min_price=10&max_price=100')
         ->assertOk()
         ->assertJsonCount(1, 'data')
         ->assertJsonPath('data.0.price', 50.0);
});

test('can filter in-stock products', function () {
    Product::factory()->inStock()->create();
    Product::factory()->outOfStock()->create();

    $this->getJson('/api/v1/products?in_stock=true')
         ->assertOk()
         ->assertJsonCount(1, 'data');
});

// 404 handling

test('returns 404 for non-existent product', function () {
    $this->getJson('/api/v1/products/non-existent-uuid')
         ->assertNotFound()
         ->assertJsonStructure(['message']);
});
