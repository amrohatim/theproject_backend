<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->vendor(),
            'name' => fake()->company(),
            'description' => fake()->paragraph(),
            'logo' => 'images/companies/default.jpg',
            'website' => fake()->url(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip_code' => fake()->postcode(),
            'country' => fake()->country(),
            'tax_id' => fake()->numerify('##########'),
            'status' => 'active',
            'business_type' => fake()->randomElement(['retail', 'wholesale', 'service']),
            'registration_number' => fake()->numerify('REG-########'),
            'can_deliver' => fake()->boolean(80), // 80% chance of being able to deliver
        ];
    }
    
    /**
     * Indicate that the company can deliver.
     */
    public function canDeliver(): static
    {
        return $this->state(fn (array $attributes) => [
            'can_deliver' => true,
        ]);
    }
    
    /**
     * Indicate that the company cannot deliver.
     */
    public function cannotDeliver(): static
    {
        return $this->state(fn (array $attributes) => [
            'can_deliver' => false,
        ]);
    }
}
