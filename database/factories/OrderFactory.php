<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shippingAddress = [
            'name' => fake()->name(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip_code' => fake()->postcode(),
            'country' => fake()->country(),
            'phone' => fake()->phoneNumber(),
        ];
        
        return [
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'total' => fake()->randomFloat(2, 50, 5000),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed']),
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'cash']),
            'shipping_address' => $shippingAddress,
            'billing_address' => $shippingAddress,
            'notes' => fake()->optional()->sentence(),
            'shipping_method' => fake()->randomElement(['vendor', 'aramex']),
            'shipping_status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered']),
            'shipping_cost' => fake()->randomFloat(2, 5, 50),
            'tracking_number' => fake()->optional()->regexify('[A-Z0-9]{10}'),
            'customer_name' => $shippingAddress['name'],
            'customer_phone' => $shippingAddress['phone'],
            'shipping_city' => $shippingAddress['city'],
            'shipping_country' => $shippingAddress['country'],
        ];
    }
    
    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
            'shipping_status' => 'pending',
        ]);
    }
    
    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'shipping_status' => 'delivered',
        ]);
    }
    
    /**
     * Indicate that the order uses vendor shipping.
     */
    public function vendorShipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'shipping_method' => 'vendor',
        ]);
    }
    
    /**
     * Indicate that the order uses Aramex shipping.
     */
    public function aramexShipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'shipping_method' => 'aramex',
        ]);
    }
}
