<?php

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Check if admin user already exists
$adminExists = User::where('email', 'admin@example.com')->exists();

if ($adminExists) {
    echo "Admin user already exists!\n";
} else {
    // Create admin user
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => 'password123', // Laravel will automatically hash this
        'role' => 'admin',
        'status' => 'active',
    ]);

    echo "Admin user created successfully!\n";
    echo "Email: admin@example.com\n";
    echo "Password: password123\n";
}

// Create vendor user if it doesn't exist
$vendorExists = User::where('email', 'vendor@example.com')->exists();

if ($vendorExists) {
    echo "Vendor user already exists!\n";
} else {
    // Create vendor user
    $user = User::create([
        'name' => 'Vendor User',
        'email' => 'vendor@example.com',
        'password' => 'password123', // Laravel will automatically hash this
        'role' => 'vendor',
        'status' => 'active',
    ]);

    echo "Vendor user created successfully!\n";
    echo "Email: vendor@example.com\n";
    echo "Password: password123\n";
}

// Create customer user if it doesn't exist
$customerExists = User::where('email', 'customer@example.com')->exists();

if ($customerExists) {
    echo "Customer user already exists!\n";
} else {
    // Create customer user
    $user = User::create([
        'name' => 'Customer User',
        'email' => 'customer@example.com',
        'password' => 'password123', // Laravel will automatically hash this
        'role' => 'customer',
        'status' => 'active',
    ]);

    echo "Customer user created successfully!\n";
    echo "Email: customer@example.com\n";
    echo "Password: password123\n";
}
