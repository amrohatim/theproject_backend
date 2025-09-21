<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Category;

echo "🔍 Checking vendor account for services seeding: gogoh3296@gmail.com\n";
echo "==========================================================\n\n";

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
    
    // Check service categories (only child categories)
    $categories = Category::where('type', 'service')
        ->where('is_active', true)
        ->whereNotNull('parent_id') // Only child categories
        ->get();

    if ($categories->isEmpty()) {
        echo "❌ No child service categories found!\n";
        echo "Please ensure there are active child service categories (categories with parent_id) in the database.\n";
        echo "Services can only be assigned to child categories, not parent categories.\n\n";
        exit(1);
    }

    echo "✅ Child service categories found: {$categories->count()}\n";
    echo "Sample child categories:\n";
    foreach ($categories->take(10) as $category) {
        $parentInfo = $category->parent ? " (Parent: {$category->parent->name})" : " (No Parent - ERROR!)";
        echo "  - {$category->name}{$parentInfo} (ID: {$category->id})\n";
    }
    if ($categories->count() > 10) {
        echo "  - ... and " . ($categories->count() - 10) . " more\n";
    }
    echo "\n";
    
    // Check if services table has required columns
    echo "✅ Checking services table structure...\n";
    $requiredColumns = [
        'branch_id', 'category_id', 'name', 'service_name_arabic', 
        'description', 'service_description_arabic', 'price', 'duration', 
        'image', 'is_available', 'home_service', 'featured', 'rating'
    ];
    
    $missingColumns = [];
    foreach ($requiredColumns as $column) {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('services', $column)) {
            $missingColumns[] = $column;
        }
    }
    
    if (!empty($missingColumns)) {
        echo "❌ Missing required columns in services table:\n";
        foreach ($missingColumns as $column) {
            echo "  - {$column}\n";
        }
        echo "\nPlease run the necessary migrations to add these columns.\n\n";
        exit(1);
    }
    
    echo "✅ All required columns present in services table.\n\n";
    
    echo "🎉 All checks passed! Ready to run the VendorServicesSeeder.\n";
    echo "\nTo run the seeder, execute:\n";
    echo "php artisan db:seed --class=VendorServicesSeeder\n\n";
    
} catch (Exception $e) {
    echo "❌ Error during check: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
