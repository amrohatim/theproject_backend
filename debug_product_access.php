<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Product Access Debug Script ===" . PHP_EOL;

// Get product 60 with all relationships
$product = \App\Models\Product::with(['branch.company', 'user'])->find(60);

if (!$product) {
    echo "❌ Product 60 not found!" . PHP_EOL;
    exit;
}

echo "✅ Product 60 found:" . PHP_EOL;
echo "- ID: {$product->id}" . PHP_EOL;
echo "- Name: {$product->name}" . PHP_EOL;
echo "- User ID: {$product->user_id}" . PHP_EOL;
echo "- Branch ID: {$product->branch_id}" . PHP_EOL;

if ($product->branch) {
    echo "- Branch Name: {$product->branch->name}" . PHP_EOL;
    echo "- Branch Company ID: {$product->branch->company_id}" . PHP_EOL;
    
    if ($product->branch->company) {
        echo "- Company Name: {$product->branch->company->name}" . PHP_EOL;
        echo "- Company User ID: {$product->branch->company->user_id}" . PHP_EOL;
    } else {
        echo "❌ Branch has no company!" . PHP_EOL;
    }
} else {
    echo "❌ Product has no branch!" . PHP_EOL;
}

if ($product->user) {
    echo "- Product User: {$product->user->name} (ID: {$product->user->id})" . PHP_EOL;
    echo "- Product User Role: {$product->user->role}" . PHP_EOL;
} else {
    echo "❌ Product has no user!" . PHP_EOL;
}

echo PHP_EOL . "=== Checking Vendor Users ===" . PHP_EOL;

// Check vendor users
$vendorUsers = \App\Models\User::where('role', 'vendor')->get();
echo "Total vendor users: {$vendorUsers->count()}" . PHP_EOL;

foreach ($vendorUsers as $vendor) {
    echo PHP_EOL . "Vendor: {$vendor->name} (ID: {$vendor->id})" . PHP_EOL;
    
    // Check if this vendor has a company
    $company = $vendor->company;
    if ($company) {
        echo "  ✅ Company: {$company->name} (ID: {$company->id})" . PHP_EOL;
        
        // Check branches for this company
        $branches = $company->branches;
        echo "  Branches: {$branches->count()}" . PHP_EOL;
        foreach ($branches as $branch) {
            echo "    - Branch: {$branch->name} (ID: {$branch->id})" . PHP_EOL;
            
            // Check if this branch matches product 60's branch
            if ($product->branch_id == $branch->id) {
                echo "      🎯 THIS BRANCH MATCHES PRODUCT 60!" . PHP_EOL;
            }
        }
        
        // Test the authorization logic from the controller
        $userBranches = \App\Models\Branch::whereHas('company', function ($query) use ($vendor) {
            $query->where('user_id', $vendor->id);
        })->pluck('id')->toArray();
        
        echo "  User branches: [" . implode(', ', $userBranches) . "]" . PHP_EOL;
        
        if (in_array($product->branch_id, $userBranches)) {
            echo "  ✅ This vendor CAN access product 60" . PHP_EOL;
        } else {
            echo "  ❌ This vendor CANNOT access product 60" . PHP_EOL;
        }
    } else {
        echo "  ❌ No company found" . PHP_EOL;
    }
}

echo PHP_EOL . "=== Authorization Logic Analysis ===" . PHP_EOL;
echo "The controller checks if product.branch_id is in the vendor's company branches." . PHP_EOL;
echo "Product 60 branch_id: {$product->branch_id}" . PHP_EOL;

if ($product->branch && $product->branch->company) {
    echo "Product 60 belongs to company: {$product->branch->company->name} (owned by user {$product->branch->company->user_id})" . PHP_EOL;
} else {
    echo "❌ Product 60 has missing branch or company relationship!" . PHP_EOL;
}
