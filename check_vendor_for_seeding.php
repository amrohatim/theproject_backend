<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Category;

echo "🔍 Checking vendor account for seeding: gogoh3296@gmail.com\n";
echo "=====================================================\n\n";

try {
    // Check if vendor exists
    $vendor = User::where('email', 'gogoh3296@gmail.com')->first();
    
    if (!$vendor) {
        echo "❌ Vendor user not found!\n";
        echo "Please ensure the vendor user with email 'gogoh3296@gmail.com' exists.\n";
        echo "You can create it manually or run the appropriate seeder first.\n\n";
        exit(1);
    }
    
    echo "✅ Vendor found!\n";
    echo "  - ID: {$vendor->id}\n";
    echo "  - Name: {$vendor->name}\n";
    echo "  - Email: {$vendor->email}\n";
    echo "  - Role: {$vendor->role}\n";
    echo "  - Status: {$vendor->status}\n\n";
    
    // Check company
    $company = $vendor->company;
    if (!$company) {
        echo "❌ Vendor has no company associated!\n";
        echo "Please ensure the vendor has a company record.\n\n";
        exit(1);
    }
    
    echo "✅ Company found!\n";
    echo "  - ID: {$company->id}\n";
    echo "  - Name: {$company->name}\n";
    echo "  - Email: {$company->email}\n\n";
    
    // Check branches
    $branches = $company->branches;
    if ($branches->isEmpty()) {
        echo "❌ No branches found for the company!\n";
        echo "Please ensure the company has at least one branch.\n\n";
        exit(1);
    }
    
    echo "✅ Branches found: {$branches->count()}\n";
    foreach ($branches as $branch) {
        echo "  - {$branch->name} (ID: {$branch->id})\n";
    }
    echo "\n";
    
    // Check categories
    $categories = Category::where('type', 'product')
        ->whereNotNull('parent_id')
        ->whereDoesntHave('children')
        ->get();
    
    if ($categories->isEmpty()) {
        echo "❌ No leaf categories found for products!\n";
        echo "Please ensure there are product categories that can be selected (leaf categories).\n\n";
        exit(1);
    }
    
    echo "✅ Product categories found: {$categories->count()}\n";
    echo "Sample categories:\n";
    foreach ($categories->take(5) as $category) {
        echo "  - {$category->name} (ID: {$category->id})\n";
    }
    if ($categories->count() > 5) {
        echo "  - ... and " . ($categories->count() - 5) . " more\n";
    }
    echo "\n";
    
    echo "🎉 All checks passed! Ready to run the VendorProductsWithColorsSeeder.\n";
    echo "\nTo run the seeder, execute:\n";
    echo "php artisan db:seed --class=VendorProductsWithColorsSeeder\n\n";
    
} catch (Exception $e) {
    echo "❌ Error during check: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
