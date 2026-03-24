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
        // Create 3 vendors first
        $vendors = Vendor::factory(3)->create();

        // Create 9 products assigned to random vendors from the pool
        Product::factory(9)->create([
            'vendor_id' => fn() => $vendors->random()->id,
        ]);

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
