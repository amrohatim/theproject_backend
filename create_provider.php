<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

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
    echo "Admin user created.\n";
}

// Create provider user if it doesn't exist
if (!User::where('email', 'provider@example.com')->exists()) {
    User::create([
        'name' => 'Provider User',
        'email' => 'provider@example.com',
        'password' => Hash::make('password'),
        'role' => 'provider',
        'phone' => '1234567891',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);
    echo "Provider user created.\n";
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
    echo "Customer user created.\n";
}

echo "Done.\n";
