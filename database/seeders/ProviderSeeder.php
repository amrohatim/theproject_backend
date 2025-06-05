<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '1234567890',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }

        // Create provider user if it doesn't exist
        $providerUser = null;
        if (!User::where('email', 'provider@example.com')->exists()) {
            $providerUser = User::create([
                'name' => 'Provider User',
                'email' => 'provider@example.com',
                'password' => Hash::make('password'),
                'role' => 'provider',
                'phone' => '1234567891',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        } else {
            $providerUser = User::where('email', 'provider@example.com')->first();
        }

        // Create provider record in providers table if it doesn't exist
        if ($providerUser && !Provider::where('user_id', $providerUser->id)->exists()) {
            Provider::create([
                'user_id' => $providerUser->id,
                'business_name' => 'Sample Provider Business',
                'business_type' => 'Food & Beverages',
                'registration_number' => 'REG123456',
                'description' => 'A sample provider business for testing purposes.',
                'address' => '123 Provider Street',
                'city' => 'Provider City',
                'state' => 'Provider State',
                'postal_code' => '12345',
                'country' => 'Provider Country',
                'website' => 'https://provider.example.com',
                'status' => 'active',
                'is_verified' => true,
                'average_rating' => 0,
                'total_ratings' => 0,
            ]);
        }

        // Create customer user if it doesn't exist
        if (!User::where('email', 'customer@example.com')->exists()) {
            User::create([
                'name' => 'Customer User',
                'email' => 'customer@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '1234567892',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }

        // Create categories if they don't exist
        $categories = [
            ['name' => 'Vegetables', 'type' => 'product'],
            ['name' => 'Fruits', 'type' => 'product'],
            ['name' => 'Bakery', 'type' => 'product'],
            ['name' => 'Dairy', 'type' => 'product'],
            ['name' => 'Meat', 'type' => 'product'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['name' => $categoryData['name']],
                ['type' => $categoryData['type']]
            );
        }

        // Get provider user
        $provider = User::where('email', 'provider@example.com')->first();

        // Create products for the provider
        if ($provider) {
            $vegetablesCategory = Category::where('name', 'Vegetables')->first();
            $fruitsCategory = Category::where('name', 'Fruits')->first();
            $bakeryCategory = Category::where('name', 'Bakery')->first();

            $products = [
                [
                    'name' => 'Organic Vegetables Pack',
                    'price' => 15.99,
                    'original_price' => 18.99,
                    'stock' => 50,
                    'description' => 'Fresh organic vegetables pack with a variety of seasonal vegetables.',
                    'is_available' => true,
                    'category_id' => $vegetablesCategory->id,
                    'user_id' => $provider->id,
                ],
                [
                    'name' => 'Fresh Fruits Basket',
                    'price' => 22.99,
                    'original_price' => 25.99,
                    'stock' => 30,
                    'description' => 'Assorted fresh fruits basket with seasonal fruits.',
                    'is_available' => true,
                    'category_id' => $fruitsCategory->id,
                    'user_id' => $provider->id,
                ],
                [
                    'name' => 'Homemade Bread',
                    'price' => 5.99,
                    'original_price' => 7.99,
                    'stock' => 20,
                    'description' => 'Freshly baked homemade bread made with organic ingredients.',
                    'is_available' => true,
                    'category_id' => $bakeryCategory->id,
                    'user_id' => $provider->id,
                ],
            ];

            foreach ($products as $productData) {
                Product::firstOrCreate(
                    ['name' => $productData['name'], 'user_id' => $provider->id],
                    $productData
                );
            }
        }
    }
}
