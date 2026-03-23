<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 5 vendors, each with 10 products
        Vendor::factory(5)
            ->has(Product::factory()->count(10))
            ->create();

        // Create an admin user for testing
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create a test customer
        User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);
    }
}
