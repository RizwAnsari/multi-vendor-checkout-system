<?php

namespace Database\Factories;

use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'name' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->optional()->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock' => $this->faker->numberBetween(0, 50),
            'is_active' => true,
            'image' => "https://picsum.photos/640/480?random={$this->faker->unique()->numberBetween(1, 1000)}",
        ];
    }
}
