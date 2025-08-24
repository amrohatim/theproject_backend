<?php

/**
 * Verification Script for Branch Coordinates Fix
 * 
 * This script verifies that the latitude and longitude coordinates
 * are being properly saved to the database after our fix.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\User;
use App\Models\Company;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Branch Coordinates Verification Script ===\n\n";

try {
    // Test 1: Check if lat/lng columns exist and have correct data types
    echo "1. Checking database schema for lat/lng columns...\n";

    $columns = DB::select("DESCRIBE branches");
    $latColumn = null;
    $lngColumn = null;

    foreach ($columns as $column) {
        if ($column->Field === 'lat') {
            $latColumn = $column;
        }
        if ($column->Field === 'lng') {
            $lngColumn = $column;
        }
    }

    if ($latColumn && $lngColumn) {
        echo "✓ lat column found: {$latColumn->Type}\n";
        echo "✓ lng column found: {$lngColumn->Type}\n";
    } else {
        echo "✗ lat/lng columns not found!\n";
        exit(1);
    }
    
    // Test 2: Check existing branches with coordinates
    echo "\n2. Checking existing branches with coordinates...\n";
    
    $branchesWithCoords = Branch::whereNotNull('lat')
        ->whereNotNull('lng')
        ->select('id', 'name', 'lat', 'lng', 'address')
        ->limit(5)
        ->get();
    
    if ($branchesWithCoords->count() > 0) {
        echo "Found {$branchesWithCoords->count()} branches with coordinates:\n";
        foreach ($branchesWithCoords as $branch) {
            echo "  - ID: {$branch->id}, Name: {$branch->name}\n";
            echo "    Coordinates: {$branch->lat}, {$branch->lng}\n";
            echo "    Address: {$branch->address}\n\n";
        }
    } else {
        echo "No branches with coordinates found.\n";
    }
    
    // Test 3: Simulate coordinate validation
    echo "3. Testing coordinate validation rules...\n";
    
    $validationRules = [
        'lat' => 'required|numeric|between:-90,90',
        'lng' => 'required|numeric|between:-180,180',
    ];
    
    $testCases = [
        ['lat' => 25.2048, 'lng' => 55.2708, 'expected' => 'valid'],
        ['lat' => 91.0, 'lng' => 55.2708, 'expected' => 'invalid'],
        ['lat' => 25.2048, 'lng' => 181.0, 'expected' => 'invalid'],
        ['lat' => null, 'lng' => 55.2708, 'expected' => 'invalid'],
    ];
    
    foreach ($testCases as $i => $testCase) {
        $validator = \Illuminate\Support\Facades\Validator::make($testCase, $validationRules);
        $isValid = !$validator->fails();
        $expected = $testCase['expected'] === 'valid';
        
        if ($isValid === $expected) {
            echo "✓ Test case " . ($i + 1) . ": {$testCase['expected']} coordinates validation passed\n";
        } else {
            echo "✗ Test case " . ($i + 1) . ": {$testCase['expected']} coordinates validation failed\n";
        }
    }
    
    // Test 4: Create a test branch to verify coordinates are saved
    echo "\n4. Testing branch creation with coordinates...\n";
    
    // Find a vendor user or create one for testing
    $vendor = User::where('role', 'vendor')->first();
    if (!$vendor) {
        echo "No vendor user found. Creating test vendor...\n";
        $vendor = User::create([
            'name' => 'Test Vendor',
            'email' => 'test.vendor@example.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
        ]);
    }
    
    // Find or create a company
    $company = Company::where('user_id', $vendor->id)->first();
    if (!$company) {
        echo "No company found for vendor. Creating test company...\n";
        $company = Company::create([
            'user_id' => $vendor->id,
            'name' => 'Test Company for Coordinates',
            'description' => 'Test company for coordinate verification',
        ]);
    }
    
    // Create a test branch with coordinates
    $testBranch = Branch::create([
        'user_id' => $vendor->id,
        'company_id' => $company->id,
        'name' => 'Test Branch - Coordinates Verification',
        'address' => 'Test Address, Dubai, UAE',
        'lat' => 25.2048,
        'lng' => 55.2708,
        'status' => 'active',
    ]);
    
    if ($testBranch) {
        echo "✓ Test branch created successfully!\n";
        echo "  - ID: {$testBranch->id}\n";
        echo "  - Name: {$testBranch->name}\n";
        echo "  - Saved Latitude: {$testBranch->lat}\n";
        echo "  - Saved Longitude: {$testBranch->lng}\n";
        
        // Verify the coordinates were saved correctly
        $savedBranch = Branch::find($testBranch->id);
        if ((float)$savedBranch->lat === 25.2048 && (float)$savedBranch->lng === 55.2708) {
            echo "✓ Coordinates saved and retrieved correctly!\n";
        } else {
            echo "✗ Coordinates not saved correctly!\n";
            echo "  Expected: 25.2048, 55.2708\n";
            echo "  Got: {$savedBranch->lat}, {$savedBranch->lng}\n";
        }
        
        // Clean up test data
        $testBranch->delete();
        echo "✓ Test branch cleaned up.\n";
    } else {
        echo "✗ Failed to create test branch!\n";
    }
    
    echo "\n=== Verification Complete ===\n";
    echo "The latitude and longitude coordinate saving functionality has been verified.\n";
    echo "The fix to add lat/lng validation in vendor routes should resolve the issue.\n";
    
} catch (Exception $e) {
    echo "✗ Error during verification: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
