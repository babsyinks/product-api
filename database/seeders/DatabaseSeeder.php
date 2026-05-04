<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed a default admin user for testing Sanctum auth
        User::factory()->create([
            'name'  => 'Test Admin',
            'email' => 'admin@example.com',
        ]);

        $this->call(ProductSeeder::class);
    }
}
