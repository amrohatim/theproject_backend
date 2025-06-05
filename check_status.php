<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Product;
use App\Models\Branch;

echo "📊 Database Status Check\n";
echo "========================\n\n";

try {
    // Check products
    $totalProducts = Product::count();
    echo "Products: {$totalProducts}\n";
    
    // Check categories
    $totalCategories = Category::where('type', 'product')->count();
    $parentCategories = Category::where('type', 'product')->whereNull('parent_id')->count();
    $subcategories = Category::where('type', 'product')->whereNotNull('parent_id')->count();
    
    echo "Categories: {$totalCategories} total\n";
    echo "  - Parent categories: {$parentCategories}\n";
    echo "  - Subcategories: {$subcategories}\n\n";
    
    // List parent categories
    echo "Parent Categories:\n";
    $parents = Category::where('type', 'product')->whereNull('parent_id')->get();
    foreach ($parents as $parent) {
        $subCount = Category::where('parent_id', $parent->id)->count();
        echo "  - {$parent->name} ({$subCount} subcategories)\n";
    }
    
    echo "\nSubcategories:\n";
    $subs = Category::where('type', 'product')->whereNotNull('parent_id')->get();
    foreach ($subs as $sub) {
        $parentName = $sub->parent ? $sub->parent->name : 'Unknown';
        echo "  - {$sub->name} (under {$parentName})\n";
    }
    
    // Check branches
    $totalBranches = Branch::count();
    echo "\nBranches: {$totalBranches}\n";
    
    // Check product images directory
    $productImagesPath = 'Products images';
    if (is_dir($productImagesPath)) {
        $imageFiles = glob($productImagesPath . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        echo "Product image files available: " . count($imageFiles) . "\n";
    } else {
        echo "Product images directory not found\n";
    }
    
    // Recovery status
    echo "\n🎯 Recovery Status:\n";
    if ($totalProducts >= 102) {
        echo "✅ SUCCESS! Database has {$totalProducts} products (target: 102+)\n";
    } else {
        $needed = 102 - $totalProducts;
        echo "⚠️  Need {$needed} more products to reach target of 102+\n";
    }
    
    if ($totalCategories >= 20) {
        echo "✅ Categories restored: {$totalCategories} categories\n";
    } else {
        echo "⚠️  Categories: {$totalCategories} (may need more comprehensive categories)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Status check completed!\n";
