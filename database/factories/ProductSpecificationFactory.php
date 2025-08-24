<?php

namespace Database\Factories;

use App\Models\ProductSpecification;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSpecification>
 */
class ProductSpecificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductSpecification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specifications = [
            'Material' => ['Cotton', 'Polyester', 'Silk', 'Wool', 'Linen'],
            'Brand' => ['Nike', 'Adidas', 'Puma', 'Under Armour', 'Reebok'],
            'Color' => ['Red', 'Blue', 'Green', 'Black', 'White'],
            'Weight' => ['100g', '250g', '500g', '1kg', '2kg'],
            'Dimensions' => ['10x10x10cm', '20x15x5cm', '30x20x10cm'],
            'Origin' => ['USA', 'China', 'Germany', 'Japan', 'Italy'],
            'Warranty' => ['1 Year', '2 Years', '3 Years', '5 Years', 'Lifetime'],
        ];

        $key = $this->faker->randomElement(array_keys($specifications));
        $value = $this->faker->randomElement($specifications[$key]);

        return [
            'product_id' => Product::factory(),
            'key' => $key,
            'value' => $value,
            'display_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    /**
     * Create a specification with a specific key-value pair.
     */
    public function withKeyValue(string $key, string $value): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => $key,
            'value' => $value,
        ]);
    }
}
