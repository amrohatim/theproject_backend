<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Company;
use App\Helpers\ImageHelper;

echo "=== Fixing Existing Logo ===\n\n";

// Test the user we know has issues
$email = 'gogofifa56@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found: $email\n";
    exit(1);
}

echo "Found user: {$user->name} ({$user->email})\n";

$company = $user->company;
if (!$company) {
    echo "No company found for this user\n";
    exit(1);
}

echo "Company: {$company->name}\n";
echo "Current logo field: " . ($company->getRawOriginal('logo') ?? 'NULL') . "\n";

// Check if there are any logo files in storage for this company
$storageCompaniesPath = storage_path('app/public/companies');
if (is_dir($storageCompaniesPath)) {
    $files = scandir($storageCompaniesPath);
    $imageFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
    });
    
    echo "Available logo files: " . implode(', ', $imageFiles) . "\n";
    
    if (!empty($imageFiles)) {
        // Use the first available image file
        $logoFilename = reset($imageFiles);
        echo "Updating company logo to: $logoFilename\n";
        
        // Update the company logo field with just the filename
        $company->update(['logo' => $logoFilename]);
        
        echo "Logo updated successfully!\n";
        
        // Test the logo URL now
        $logoUrl = $company->fresh()->logo;
        echo "New logo URL: $logoUrl\n";
        
        // Test ImageHelper
        $imageHelper = new ImageHelper();
        $directImageUrl = $imageHelper->getImageUrl('companies/' . $logoFilename);
        echo "Direct ImageHelper URL: $directImageUrl\n";
        
        // Check if the file is accessible
        $logoPath = parse_url($logoUrl, PHP_URL_PATH);
        $fullPath = public_path($logoPath);
        echo "Full file path: $fullPath\n";
        echo "File exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . "\n";
        
    } else {
        echo "No logo files found in storage\n";
    }
} else {
    echo "Storage companies directory does not exist\n";
}

echo "\n=== Fix Complete ===\n";