<?php

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Get all users
$users = User::all();

echo "Found " . $users->count() . " users in the database.\n";

// Update admin user
$admin = User::where('email', 'admin@example.com')->first();
if ($admin) {
    echo "Updating admin user password...\n";
    $admin->password = Hash::make('password123');
    $admin->save();
    echo "Admin user password updated.\n";
} else {
    echo "Admin user not found. Creating...\n";
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'status' => 'active',
    ]);
    echo "Admin user created.\n";
}

// Update vendor user
$vendor = User::where('email', 'vendor@example.com')->first();
if ($vendor) {
    echo "Updating vendor user password...\n";
    $vendor->password = Hash::make('password123');
    $vendor->save();
    echo "Vendor user password updated.\n";
} else {
    echo "Vendor user not found. Creating...\n";
    User::create([
        'name' => 'Vendor User',
        'email' => 'vendor@example.com',
        'password' => Hash::make('password123'),
        'role' => 'vendor',
        'status' => 'active',
    ]);
    echo "Vendor user created.\n";
}

// Update customer user
$customer = User::where('email', 'customer@example.com')->first();
if ($customer) {
    echo "Updating customer user password...\n";
    $customer->password = Hash::make('password123');
    $customer->save();
    echo "Customer user password updated.\n";
} else {
    echo "Customer user not found. Creating...\n";
    User::create([
        'name' => 'Customer User',
        'email' => 'customer@example.com',
        'password' => Hash::make('password123'),
        'role' => 'customer',
        'status' => 'active',
    ]);
    echo "Customer user created.\n";
}

echo "All user passwords have been updated or users created.\n";
