<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$provider = User::where('role', 'provider')->first();

if ($provider) {
    echo "Testing Updated Provider Middleware Logic:\n";
    echo "Provider ID: " . $provider->id . "\n";
    echo "Email: " . $provider->email . "\n";
    echo "Email Verified At: " . ($provider->email_verified_at ? $provider->email_verified_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "Phone Verified At: " . ($provider->phone_verified_at ? $provider->phone_verified_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    
    // Test the updated middleware conditions
    echo "\nUpdated Middleware Checks:\n";
    echo "1. Role is provider: " . ($provider->role === 'provider' ? 'PASS' : 'FAIL') . "\n";
    echo "2. Has License: " . ($provider->hasLicense() ? 'PASS' : 'FAIL') . "\n";
    echo "3. License Status: " . ($provider->getLicenseStatus() ?? 'NULL') . "\n";
    echo "4. License Status is active: " . ($provider->getLicenseStatus() === 'active' ? 'PASS' : 'FAIL') . "\n";
    echo "5. Email verification check: REMOVED (no longer checked)\n";
    echo "6. Phone verified check (!phone_verified_at): " . (!$provider->phone_verified_at ? 'REDIRECT TO PHONE VERIFY' : 'PASS') . "\n";
    
    echo "\nFinal Result: ";
    if ($provider->role !== 'provider') {
        echo "FAIL - Not a provider\n";
    } elseif (!$provider->hasLicense()) {
        echo "REDIRECT TO LICENSE UPLOAD\n";
    } elseif ($provider->getLicenseStatus() !== 'active') {
        echo "REDIRECT TO LICENSE STATUS\n";
    } elseif (!$provider->phone_verified_at) {
        echo "REDIRECT TO PHONE VERIFICATION\n";
    } else {
        echo "✅ ALLOW ACCESS TO DASHBOARD\n";
    }
} else {
    echo "No provider found in database\n";
}
