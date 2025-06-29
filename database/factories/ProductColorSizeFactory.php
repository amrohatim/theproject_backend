<?php

namespace Database\Factories;

use App\Models\ProductColorSize;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductColorSize>
 */
class ProductColorSizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductColorSize::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'product_color_id' => ProductColor::factory(),
            'product_size_id' => ProductSize::factory(),
            'stock' => $this->faker->numberBetween(0, 50),
            'price_adjustment' => $this->faker->randomFloat(2, 0, 20),
            'is_available' => $this->faker->boolean(80), // 80% chance of being available
        ];
    }

    /**
     * Indicate that the color-size combination is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'is_available' => false,
        ]);
    }

    /**
     * Indicate that the color-size combination is in stock.
     */
    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $this->faker->numberBetween(1, 50),
            'is_available' => true,
        ]);
    }
}
