<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can register and receive a token', function () {
    $this->postJson('/api/v1/register', [
        'name'                  => 'Jane Doe',
        'email'                 => 'jane@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])
    ->assertCreated()
    ->assertJsonStructure(['token', 'token_type'])
    ->assertJsonPath('token_type', 'Bearer');
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'secret123',
    ])
    ->assertOk()
    ->assertJsonStructure(['token', 'token_type']);
});

test('login fails with invalid credentials', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ])->assertUnprocessable();
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
         ->postJson('/api/v1/logout')
         ->assertOk()
         ->assertJsonPath('message', 'Logged out successfully.');
});

test('authenticated user can fetch their profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
         ->getJson('/api/v1/me')
         ->assertOk()
         ->assertJsonPath('email', $user->email);
});
