<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Company;

echo "Fixing vendor logo database path for: gogofifa56@gmail.com\n";

$user = User::where('email', 'gogofifa56@gmail.com')->first();

if ($user) {
    echo "User found: {$user->name}\n";
    
    $company = Company::where('user_id', $user->id)->first();
    if ($company) {
        echo "Company found: {$company->name}\n";
        echo "Current logo: {$company->logo}\n";
        
        // Update database with correct relative path
        $correctPath = 'storage/companies/company_26_1753226164.png';
        $company->logo = $correctPath;
        $company->save();
        
        echo "Database updated with correct path: {$correctPath}\n";
        
        // Verify the file exists
        $fullPath = public_path($correctPath);
        echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        echo "Full file path: {$fullPath}\n";
        
        // Reload and verify
        $company->refresh();
        echo "Verified logo path in database: {$company->logo}\n";
    }
}

echo "Fix completed\n";