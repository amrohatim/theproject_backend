<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Helpers\UnsplashImageHelper;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip creating admin user as it already exists
        // Check if admin user exists
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '1234567890',
                'profile_image' => UnsplashImageHelper::downloadAndSaveImage('business person', 'public/images/users/admin.jpg', 400, 400),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }

        // Create vendor users
        $vendors = [
            [
                'name' => 'John Vendor',
                'email' => 'john@vendor.com',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'phone' => '2345678901',
                'profile_image' => UnsplashImageHelper::downloadAndSaveImage('business man', 'public/images/users/vendor1.jpg', 400, 400),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Vendor',
                'email' => 'jane@vendor.com',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'phone' => '3456789012',
                'profile_image' => UnsplashImageHelper::downloadAndSaveImage('business woman', 'public/images/users/vendor2.jpg', 400, 400),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mike Vendor',
                'email' => 'mike@vendor.com',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'phone' => '4567890123',
                'profile_image' => UnsplashImageHelper::downloadAndSaveImage('entrepreneur', 'public/images/users/vendor3.jpg', 400, 400),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($vendors as $vendor) {
            // Check if vendor already exists
            if (!User::where('email', $vendor['email'])->exists()) {
                User::create($vendor);
            }
        }

        // Create customer users
        $customers = [
            [
                'name' => 'Alice Customer',
                'email' => 'alice@customer.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '5678901234',
                'profile_image' => UnsplashImageHelper::downloadAndSaveImage('woman portrait', 'public/images/users/customer1.jpg', 400, 400),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Bob Customer',
                'email' => 'bob@customer.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '6789012345',
                'profile_image' => UnsplashImageHelper::downloadAndSaveImage('man portrait', 'public/images/users/customer2.jpg', 400, 400),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Charlie Customer',
                'email' => 'charlie@customer.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '7890123456',
                'profile_image' => UnsplashImageHelper::downloadAndSaveImage('young man', 'public/images/users/customer3.jpg', 400, 400),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Diana Customer',
                'email' => 'diana@customer.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '8901234567',
                'profile_image' => UnsplashImageHelper::downloadAndSaveImage('young woman', 'public/images/users/customer4.jpg', 400, 400),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($customers as $customer) {
            // Check if customer already exists
            if (!User::where('email', $customer['email'])->exists()) {
                User::create($customer);
            }
        }
    }
}
