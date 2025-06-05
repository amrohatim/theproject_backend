<?php

// This script tests the company logo upload functionality

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "Testing company logo upload functionality...\n";

// Create a test image (simple file with content)
$testImagePath = storage_path('app/test_image.jpg');
file_put_contents($testImagePath, 'Test image content');
echo "Created test image file at: $testImagePath\n";

echo "Created test image at: $testImagePath\n";

// Create a test user if needed
$testUser = User::where('email', 'test_vendor@example.com')->first();
if (!$testUser) {
    $testUser = User::create([
        'name' => 'Test Vendor',
        'email' => 'test_vendor@example.com',
        'password' => bcrypt('password'),
        'role' => 'vendor',
        'status' => 'active',
    ]);
    echo "Created test user with ID: {$testUser->id}\n";
} else {
    echo "Using existing test user with ID: {$testUser->id}\n";
}

// Create or get a test company
$testCompany = Company::where('user_id', $testUser->id)->first();
if (!$testCompany) {
    $testCompany = Company::create([
        'user_id' => $testUser->id,
        'name' => 'Test Company',
        'status' => 'active',
    ]);
    echo "Created test company with ID: {$testCompany->id}\n";
} else {
    echo "Using existing test company with ID: {$testCompany->id}\n";
}

// Create an uploaded file instance
$uploadedFile = new UploadedFile(
    $testImagePath,
    'test_image.jpg',
    'image/jpeg',
    null,
    true
);

echo "Created uploaded file instance\n";

// Test storing the file
try {
    // Store the file
    $logoPath = $uploadedFile->store('companies', 'public');
    echo "Stored file at: $logoPath\n";

    // Get the URL
    $url = Storage::url($logoPath);
    echo "URL: $url\n";

    // Update the company
    $oldLogo = $testCompany->logo;
    $testCompany->logo = $url;
    $testCompany->save();

    echo "Updated company with new logo URL\n";

    // Check if the file exists in storage
    $storageFilePath = storage_path("app/public/$logoPath");
    if (file_exists($storageFilePath)) {
        echo "File exists in storage at: $storageFilePath\n";
    } else {
        echo "File does not exist in storage at: $storageFilePath\n";
    }

    // Create the public directory if it doesn't exist
    $publicDir = public_path('images/companies');
    if (!file_exists($publicDir)) {
        mkdir($publicDir, 0755, true);
        echo "Created directory: $publicDir\n";
    }

    // Copy the file to the public directory
    $filename = basename($logoPath);
    $publicFilePath = "$publicDir/$filename";
    if (copy($storageFilePath, $publicFilePath)) {
        echo "Copied file to public directory at: $publicFilePath\n";
    } else {
        echo "Failed to copy file to public directory\n";
    }

    // Check if the file exists in the public directory
    if (file_exists($publicFilePath)) {
        echo "File exists in public directory at: $publicFilePath\n";
    } else {
        echo "File does not exist in public directory at: $publicFilePath\n";
    }

    // Clean up
    if ($oldLogo) {
        $oldLogoPath = str_replace('/storage/', '', $oldLogo);
        if (Storage::disk('public')->exists($oldLogoPath)) {
            Storage::disk('public')->delete($oldLogoPath);
            echo "Deleted old logo from storage: $oldLogoPath\n";
        }
    }

    // Delete the test image
    unlink($testImagePath);
    echo "Deleted test image\n";

    echo "Test completed successfully\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
