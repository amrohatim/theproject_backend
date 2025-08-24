<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Vendor Authentication for Product 60 ===" . PHP_EOL;

// Get the vendor user who should own product 60
$vendor = \App\Models\User::find(96); // Luffy
if (!$vendor) {
    echo "❌ Vendor user 96 (Luffy) not found!" . PHP_EOL;
    exit;
}

echo "✅ Found vendor: {$vendor->name} (ID: {$vendor->id})" . PHP_EOL;
echo "- Role: {$vendor->role}" . PHP_EOL;
echo "- Email: {$vendor->email}" . PHP_EOL;

// Check if vendor has a company
$company = $vendor->company;
if (!$company) {
    echo "❌ Vendor has no company!" . PHP_EOL;
    exit;
}

echo "✅ Vendor has company: {$company->name} (ID: {$company->id})" . PHP_EOL;

// Check branches
$branches = $company->branches;
echo "✅ Company has {$branches->count()} branch(es):" . PHP_EOL;
foreach ($branches as $branch) {
    echo "  - {$branch->name} (ID: {$branch->id})" . PHP_EOL;
}

// Get product 60
$product = \App\Models\Product::find(60);
if (!$product) {
    echo "❌ Product 60 not found!" . PHP_EOL;
    exit;
}

echo "✅ Product 60 found: {$product->name}" . PHP_EOL;
echo "- Branch ID: {$product->branch_id}" . PHP_EOL;

// Test the authorization logic manually
$userBranches = \App\Models\Branch::whereHas('company', function ($query) use ($vendor) {
    $query->where('user_id', $vendor->id);
})->pluck('id')->toArray();

echo "✅ Vendor's branch IDs: [" . implode(', ', $userBranches) . "]" . PHP_EOL;

if (in_array($product->branch_id, $userBranches)) {
    echo "✅ Authorization should PASS - vendor can access product 60" . PHP_EOL;
} else {
    echo "❌ Authorization should FAIL - vendor cannot access product 60" . PHP_EOL;
}

echo PHP_EOL . "=== Testing API Endpoint Simulation ===" . PHP_EOL;

// Simulate the controller logic
try {
    // Simulate Auth::id() returning the vendor's ID
    $authUserId = $vendor->id;
    
    $userBranchesFromAuth = \App\Models\Branch::whereHas('company', function ($query) use ($authUserId) {
        $query->where('user_id', $authUserId);
    })->pluck('id')->toArray();
    
    echo "Simulated Auth::id(): {$authUserId}" . PHP_EOL;
    echo "User branches from auth: [" . implode(', ', $userBranchesFromAuth) . "]" . PHP_EOL;
    
    if (!in_array($product->branch_id, $userBranchesFromAuth)) {
        echo "❌ SIMULATED 403 ERROR: You do not have permission to edit this product." . PHP_EOL;
    } else {
        echo "✅ SIMULATED SUCCESS: Authorization passed!" . PHP_EOL;
        
        // Test the actual data retrieval
        $product->load(['specifications', 'colors', 'sizes', 'branches']);
        echo "✅ Product data loaded successfully" . PHP_EOL;
        echo "- Colors: {$product->colors->count()}" . PHP_EOL;
        echo "- Sizes: {$product->sizes->count()}" . PHP_EOL;
        echo "- Specifications: {$product->specifications->count()}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Error in simulation: {$e->getMessage()}" . PHP_EOL;
}

echo PHP_EOL . "=== Checking Current Session ===" . PHP_EOL;

// Check if there's a current session
try {
    // Start session to check current auth
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    echo "Session ID: " . session_id() . PHP_EOL;
    echo "Session data: " . print_r($_SESSION, true) . PHP_EOL;
    
} catch (Exception $e) {
    echo "Session check error: {$e->getMessage()}" . PHP_EOL;
}
