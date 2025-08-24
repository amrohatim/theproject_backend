<?php

namespace Database\Factories;

use App\Models\ProductColor;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductColor>
 */
class ProductColorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductColor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => $this->faker->colorName(),
            'color_code' => $this->faker->hexColor(),
            'image' => 'product-colors/' . $this->faker->uuid() . '.jpg',
            'price_adjustment' => $this->faker->randomFloat(2, 0, 50),
            'stock' => $this->faker->numberBetween(0, 100),
            'display_order' => $this->faker->numberBetween(0, 10),
            'is_default' => false,
        ];
    }

    /**
     * Indicate that the color is the default color.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
