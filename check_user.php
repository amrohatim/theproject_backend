<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Merchant;

echo "🔍 Checking user: gogofifa56@gmail.com\n";
echo "=====================================\n\n";

$user = User::where('email', 'gogofifa56@gmail.com')->first();

if ($user) {
    echo "✅ User found!\n";
    echo "  - Name: {$user->name}\n";
    echo "  - Email: {$user->email}\n";
    echo "  - Role: {$user->role}\n";
    echo "  - Status: {$user->status}\n";
    echo "  - Registration step: {$user->registration_step}\n";
    echo "  - Email verified: " . ($user->email_verified_at ? 'Yes (' . $user->email_verified_at . ')' : 'No') . "\n";
    echo "  - Phone verified: " . ($user->phone_verified_at ? 'Yes (' . $user->phone_verified_at . ')' : 'No') . "\n";
    
    // Check merchant record
    $merchant = $user->merchantRecord;
    if ($merchant) {
        echo "  - Merchant record: ✅ Found\n";
        echo "    - Business name: {$merchant->business_name}\n";
        echo "    - Status: {$merchant->status}\n";
        echo "    - License status: {$merchant->license_status}\n";
        echo "    - Is verified: " . ($merchant->is_verified ? 'Yes' : 'No') . "\n";
    } else {
        echo "  - Merchant record: ❌ Not found\n";
    }
} else {
    echo "❌ User not found!\n";
    echo "Creating user with email: gogofifa56@gmail.com\n";
    
    // Create the user
    $user = User::create([
        'name' => 'Test Merchant User',
        'email' => 'gogofifa56@gmail.com',
        'password' => bcrypt('Fifa2021'),
        'role' => 'merchant',
        'phone' => '+971501234567',
        'email_verified_at' => now(),
        'phone_verified' => true,
        'phone_verified_at' => now(),
        'registration_step' => 'verified',
        'status' => 'active',
    ]);
    
    echo "✅ User created successfully!\n";
    echo "  - ID: {$user->id}\n";
    echo "  - Name: {$user->name}\n";
    echo "  - Email: {$user->email}\n";
    echo "  - Role: {$user->role}\n";
    
    // Create merchant record
    $merchant = Merchant::create([
        'user_id' => $user->id,
        'business_name' => 'Test Merchant Business',
        'business_type' => 'General Trading',
        'description' => 'Test merchant business for login testing',
        'address' => 'Dubai, UAE',
        'city' => 'Dubai',
        'state' => 'Dubai',
        'postal_code' => '12345',
        'country' => 'UAE',
        'emirate' => 'Dubai',
        'status' => 'active',
        'is_verified' => true,
        'license_status' => 'active',
    ]);
    
    echo "✅ Merchant record created successfully!\n";
    echo "  - Business name: {$merchant->business_name}\n";
    echo "  - Status: {$merchant->status}\n";
}

// Fix email verification if needed
if (!$user->email_verified_at) {
    echo "\n🔧 Fixing email verification...\n";
    $user->email_verified_at = now();
    $user->save();
    echo "✅ Email verification timestamp set\n";
}

echo "\n🔍 Testing login redirect logic...\n";
echo "==================================\n";

// Test the redirect logic from routes/web.php
if ($user->role === 'merchant') {
    echo "✅ User role is 'merchant' - should redirect to merchant.dashboard\n";
    echo "  - Expected route: " . route('merchant.dashboard') . "\n";
} else {
    echo "❌ User role is '{$user->role}' - will not redirect to merchant dashboard\n";
}

// Test middleware conditions
echo "\n🔍 Testing middleware conditions...\n";
echo "====================================\n";

echo "Registration step: {$user->registration_step} " . ($user->registration_step === 'verified' ? '✅' : '❌') . "\n";
echo "Phone verified: " . ($user->phone_verified_at ? '✅ Yes' : '❌ No') . "\n";
echo "Email verified: " . ($user->email_verified_at ? '✅ Yes' : '❌ No') . "\n";

if ($merchant) {
    echo "Merchant status: {$merchant->status} " . ($merchant->status === 'active' ? '✅' : '❌') . "\n";
    echo "License status: {$merchant->license_status} " . ($merchant->license_status === 'verified' ? '✅' : '❌') . "\n";
}

echo "\n✅ Check completed!\n";
