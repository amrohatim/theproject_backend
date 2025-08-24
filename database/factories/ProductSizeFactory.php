<?php

namespace Database\Factories;

use App\Models\ProductSize;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSize>
 */
class ProductSizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductSize::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $size = $this->faker->randomElement($sizes);
        
        return [
            'product_id' => Product::factory(),
            'size_category_id' => null,
            'standardized_size_id' => null,
            'name' => $size,
            'value' => $size,
            'additional_info' => $this->faker->optional()->sentence(),
            'price_adjustment' => $this->faker->randomFloat(2, 0, 25),
            'stock' => $this->faker->numberBetween(0, 50),
            'display_order' => $this->faker->numberBetween(0, 10),
            'is_default' => false,
        ];
    }

    /**
     * Indicate that the size is the default size.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
