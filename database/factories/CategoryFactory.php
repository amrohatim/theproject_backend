<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'image' => 'images/categories/default.jpg',
            'parent_id' => null,
            'type' => fake()->randomElement(['product', 'service']),
            'icon' => fake()->randomElement(['shopping-cart', 'tools', 'laptop', 'mobile', 'home']),
        ];
    }
    
    /**
     * Indicate that the category is a product category.
     */
    public function product(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'product',
        ]);
    }
    
    /**
     * Indicate that the category is a service category.
     */
    public function service(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'service',
        ]);
    }
    
    /**
     * Indicate that the category is a subcategory.
     */
    public function subcategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => \App\Models\Category::factory(),
        ]);
    }
}
