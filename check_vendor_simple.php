<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Company;

echo "🔍 Checking vendor account: gogofifa56@gmail.com\n";
echo "=============================================\n\n";

try {
    echo "Searching for user...\n";
    $user = User::where('email', 'gogofifa56@gmail.com')->first();
    echo "User query completed.\n";
} catch (Exception $e) {
    echo "Error searching for user: " . $e->getMessage() . "\n";
    exit(1);
}

if ($user) {
    echo "✅ User found!\n";
    echo "  - ID: {$user->id}\n";
    echo "  - Name: {$user->name}\n";
    echo "  - Email: {$user->email}\n";
    echo "  - Role: {$user->role}\n";
    echo "  - Registration step: {$user->registration_step}\n";
    
    // Check company record
    $company = Company::where('user_id', $user->id)->first();
    if ($company) {
        echo "\n✅ Company record found!\n";
        echo "  - Company ID: {$company->id}\n";
        echo "  - Company name: {$company->name}\n";
        echo "  - Logo: " . ($company->logo ?? 'NULL') . "\n";
        echo "  - Email: {$company->email}\n";
        echo "  - Created at: {$company->created_at}\n";
        
        // Check if logo file exists in storage
        if ($company->logo) {
            $logoPath = public_path($company->logo);
            echo "  - Logo file exists: " . (file_exists($logoPath) ? 'Yes' : 'No') . "\n";
            echo "  - Logo file path: {$logoPath}\n";
        }
    } else {
        echo "\n❌ Company record not found!\n";
    }
    
} else {
    echo "❌ User not found!\n";
}

echo "\n✅ Check completed!\n";