<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
            'branch_id' => Branch::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'sale_price' => fake()->optional(0.3)->randomFloat(2, 5, 900),
            'stock' => fake()->numberBetween(0, 100),
            'sku' => fake()->unique()->regexify('[A-Z]{3}[0-9]{5}'),
            'image' => 'images/products/default.jpg',
            'status' => 'active',
            'featured' => fake()->boolean(20), // 20% chance of being featured
        ];
    }
    
    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }
}
