<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Company;

echo "🔍 Checking vendor account: gogofifa56@gmail.com\n";
echo "=============================================\n\n";

$user = User::where('email', 'gogofifa56@gmail.com')->first();

if ($user) {
    echo "✅ User found!\n";
    echo "  - ID: {$user->id}\n";
    echo "  - Name: {$user->name}\n";
    echo "  - Email: {$user->email}\n";
    echo "  - Role: {$user->role}\n";
    echo "  - Status: {$user->status}\n";
    echo "  - Registration step: {$user->registration_step}\n";
    echo "  - Email verified: " . ($user->email_verified_at ? 'Yes (' . $user->email_verified_at . ')' : 'No') . "\n";
    echo "  - Phone verified: " . ($user->phone_verified_at ? 'Yes (' . $user->phone_verified_at . ')' : 'No') . "\n";
    
    // Check company record
    $company = Company::where('user_id', $user->id)->first();
    if ($company) {
        echo "\n✅ Company record found!\n";
        echo "  - Company ID: {$company->id}\n";
        echo "  - Company name: {$company->name}\n";
        echo "  - Logo: " . ($company->logo ?? 'NULL') . "\n";
        echo "  - Email: {$company->email}\n";
        echo "  - Contact 1: {$company->contact_number_1}\n";
        echo "  - Contact 2: " . ($company->contact_number_2 ?? 'Not set') . "\n";
        echo "  - Created at: {$company->created_at}\n";
        echo "  - Updated at: {$company->updated_at}\n";
        
        // Check if logo file exists in storage
        if ($company->logo) {
            $logoPath = public_path($company->logo);
            echo "  - Logo file exists: " . (file_exists($logoPath) ? 'Yes' : 'No') . "\n";
            echo "  - Logo file path: {$logoPath}\n";
        }
    } else {
        echo "\n❌ Company record not found!\n";
    }
    
    // Check session data if any
    echo "\n🔍 Checking for any session data...\n";
    if (session()->has('vendor_registration')) {
        $sessionData = session('vendor_registration');
        echo "  - Session data found:\n";
        print_r($sessionData);
    } else {
        echo "  - No session data found\n";
    }
    
} else {
    echo "❌ User not found!\n";
}

echo "\n✅ Check completed!\n";