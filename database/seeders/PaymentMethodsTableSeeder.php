<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        // Create payment methods for each user
        foreach ($users as $user) {
            // Create a Visa credit card
            PaymentMethod::create([
                'user_id' => $user->id,
                'provider_type' => 'stripe',
                'payment_type' => 'credit_card',
                'name' => 'Visa Card',
                'card_brand' => 'visa',
                'last_four' => rand(1000, 9999),
                'expiry_month' => rand(1, 12),
                'expiry_year' => rand(2024, 2030),
                'billing_email' => $user->email,
                'is_default' => true,
                'meta_data' => json_encode(['card_type' => 'credit']),
            ]);

            // Create a Mastercard credit card (not default)
            PaymentMethod::create([
                'user_id' => $user->id,
                'provider_type' => 'stripe',
                'payment_type' => 'credit_card',
                'name' => 'Mastercard',
                'card_brand' => 'mastercard',
                'last_four' => rand(1000, 9999),
                'expiry_month' => rand(1, 12),
                'expiry_year' => rand(2024, 2030),
                'billing_email' => $user->email,
                'is_default' => false,
                'meta_data' => json_encode(['card_type' => 'credit']),
            ]);

            // Create a PayPal payment method
            PaymentMethod::create([
                'user_id' => $user->id,
                'provider_type' => 'paypal',
                'payment_type' => 'paypal',
                'name' => 'PayPal Account',
                'billing_email' => $user->email,
                'is_default' => false,
                'meta_data' => json_encode(['account_type' => 'personal']),
            ]);
        }
    }
}
