<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Company;
use App\Helpers\ImageHelper;

echo "=== Testing Logo Fix ===\n\n";

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
echo "Raw logo field in database: " . ($company->logo ?? 'NULL') . "\n";

// Test the logo accessor
$logoUrl = $company->logo;
echo "Logo URL from accessor: $logoUrl\n";

// Test ImageHelper directly
if ($company->getRawOriginal('logo')) {
    $directImageUrl = ImageHelper::getFullImageUrl('companies/' . $company->getRawOriginal('logo'));
    echo "Direct ImageHelper URL: $directImageUrl\n";
}

// Check if files exist in storage
echo "\n=== File System Check ===\n";
$storageCompaniesPath = storage_path('app/public/companies');
$publicCompaniesPath = public_path('storage/companies');

echo "Storage path: $storageCompaniesPath\n";
echo "Public path: $publicCompaniesPath\n";

if (is_dir($storageCompaniesPath)) {
    $files = scandir($storageCompaniesPath);
    $imageFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
    });
    echo "Files in storage/app/public/companies: " . implode(', ', $imageFiles) . "\n";
} else {
    echo "Storage companies directory does not exist\n";
}

if (is_dir($publicCompaniesPath)) {
    $files = scandir($publicCompaniesPath);
    $imageFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
    });
    echo "Files in public/storage/companies: " . implode(', ', $imageFiles) . "\n";
} else {
    echo "Public companies directory does not exist\n";
}

// Test if the logo URL is accessible
echo "\n=== URL Accessibility Test ===\n";
if ($logoUrl && $logoUrl !== 'http://localhost:8000/images/placeholder.png') {
    $logoPath = parse_url($logoUrl, PHP_URL_PATH);
    $fullPath = public_path($logoPath);
    echo "Full file path: $fullPath\n";
    echo "File exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . "\n";
    if (file_exists($fullPath)) {
        echo "File size: " . filesize($fullPath) . " bytes\n";
    }
} else {
    echo "Logo URL is placeholder or empty\n";
}

echo "\n=== Test Complete ===\n";