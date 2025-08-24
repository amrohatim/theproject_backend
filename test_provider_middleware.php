<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$provider = User::where('role', 'provider')->first();

if ($provider) {
    echo "Testing Provider Middleware Logic:\n";
    echo "Provider ID: " . $provider->id . "\n";
    echo "Email Verified At: " . ($provider->email_verified_at ? $provider->email_verified_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "Phone Verified At: " . ($provider->phone_verified_at ? $provider->phone_verified_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    
    // Test the middleware conditions
    echo "\nMiddleware Checks:\n";
    echo "Role is provider: " . ($provider->role === 'provider' ? 'YES' : 'NO') . "\n";
    echo "Has License: " . ($provider->hasLicense() ? 'YES' : 'NO') . "\n";
    echo "License Status: " . ($provider->getLicenseStatus() ?? 'NULL') . "\n";
    echo "License Status is active: " . ($provider->getLicenseStatus() === 'active' ? 'YES' : 'NO') . "\n";
    echo "Email verified check (!email_verified_at): " . (!$provider->email_verified_at ? 'REDIRECT TO EMAIL VERIFY' : 'PASS') . "\n";
    echo "Phone verified check (!phone_verified_at): " . (!$provider->phone_verified_at ? 'REDIRECT TO PHONE VERIFY' : 'PASS') . "\n";
    
    echo "\nConclusion: ";
    if (!$provider->email_verified_at) {
        echo "WOULD REDIRECT TO EMAIL VERIFICATION\n";
    } elseif (!$provider->phone_verified_at) {
        echo "WOULD REDIRECT TO PHONE VERIFICATION\n";
    } else {
        echo "WOULD ALLOW ACCESS TO DASHBOARD\n";
    }
} else {
    echo "No provider found in database\n";
}
