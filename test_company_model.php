<?php

// This script tests the Company model

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "Testing Company model...\n";

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

// Test updating the company logo
$logoUrl = '/storage/companies/test_logo.jpg';
$testCompany->logo = $logoUrl;
$testCompany->save();

// Verify the logo was saved
$updatedCompany = Company::find($testCompany->id);
echo "Company logo before update: " . ($testCompany->getOriginal('logo') ?? 'null') . "\n";
echo "Company logo after update: " . ($updatedCompany->logo ?? 'null') . "\n";

if ($updatedCompany->logo === $logoUrl) {
    echo "Logo was saved correctly.\n";
} else {
    echo "WARNING: Logo was not saved correctly.\n";
    echo "Expected: $logoUrl\n";
    echo "Actual: " . ($updatedCompany->logo ?? 'null') . "\n";
}

// Check the database directly
$dbCompany = DB::table('companies')->where('id', $testCompany->id)->first();
echo "Company logo in database: " . ($dbCompany->logo ?? 'null') . "\n";

if ($dbCompany->logo === $logoUrl) {
    echo "Logo is correctly stored in the database.\n";
} else {
    echo "WARNING: Logo is not correctly stored in the database.\n";
    echo "Expected: $logoUrl\n";
    echo "Actual: " . ($dbCompany->logo ?? 'null') . "\n";
}

// Test the fillable attributes
echo "\nChecking fillable attributes...\n";
$fillable = (new Company())->getFillable();
echo "Fillable attributes: " . implode(', ', $fillable) . "\n";

if (in_array('logo', $fillable)) {
    echo "Logo is in the fillable attributes.\n";
} else {
    echo "WARNING: Logo is not in the fillable attributes.\n";
}

echo "\nCompany model test completed.\n";
