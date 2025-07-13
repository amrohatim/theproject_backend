<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$provider = User::where('role', 'provider')->first();

if ($provider) {
    echo "Provider Found:\n";
    echo "ID: " . $provider->id . "\n";
    echo "Email: " . $provider->email . "\n";
    echo "Email Verified At: " . ($provider->email_verified_at ? $provider->email_verified_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "Phone Verified At: " . ($provider->phone_verified_at ? $provider->phone_verified_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "Registration Status: " . ($provider->registration_status ?? 'NULL') . "\n";
    echo "Has License: " . ($provider->hasLicense() ? 'Yes' : 'No') . "\n";
    if ($provider->hasLicense()) {
        echo "License Status: " . $provider->getLicenseStatus() . "\n";
    }
} else {
    echo "No provider found in database\n";
}
