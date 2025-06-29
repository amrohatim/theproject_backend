<?php

/**
 * Demonstration script for Product Cascading Deletion
 * 
 * This script demonstrates how the cascading deletion functionality works
 * when a product is deleted from the Laravel marketplace application.
 * 
 * Run this script with: php demo_cascading_deletion.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use App\Models\ProductSpecification;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Product Cascading Deletion Demonstration ===\n\n";

try {
    // Find an existing product or create a demo one
    $product = Product::with(['colors', 'sizes', 'colorSizes', 'specifications'])->first();
    
    if (!$product) {
        echo "No products found in the database. Please create some test data first.\n";
        echo "You can use the Laravel seeders or create products through the admin panel.\n";
        exit(1);
    }
    
    echo "Found product: {$product->name} (ID: {$product->id})\n";
    echo "Price: \${$product->price}\n";
    echo "Stock: {$product->stock}\n\n";
    
    // Show related data before deletion
    $colorsCount = $product->colors()->count();
    $sizesCount = $product->sizes()->count();
    $colorSizesCount = $product->colorSizes()->count();
    $specificationsCount = $product->specifications()->count();
    
    echo "Related data BEFORE deletion:\n";
    echo "- Colors: {$colorsCount}\n";
    echo "- Sizes: {$sizesCount}\n";
    echo "- Color-Size combinations: {$colorSizesCount}\n";
    echo "- Specifications: {$specificationsCount}\n\n";
    
    if ($colorsCount > 0) {
        echo "Colors:\n";
        foreach ($product->colors as $color) {
            echo "  - {$color->name} ({$color->color_code}) - Image: " . ($color->image ? 'Yes' : 'No') . "\n";
        }
        echo "\n";
    }
    
    if ($sizesCount > 0) {
        echo "Sizes:\n";
        foreach ($product->sizes as $size) {
            echo "  - {$size->name} ({$size->value})\n";
        }
        echo "\n";
    }
    
    if ($specificationsCount > 0) {
        echo "Specifications:\n";
        foreach ($product->specifications as $spec) {
            echo "  - {$spec->key}: {$spec->value}\n";
        }
        echo "\n";
    }
    
    // Store IDs for verification after deletion
    $productId = $product->id;
    $colorIds = $product->colors->pluck('id')->toArray();
    $sizeIds = $product->sizes->pluck('id')->toArray();
    $colorSizeIds = $product->colorSizes->pluck('id')->toArray();
    $specificationIds = $product->specifications->pluck('id')->toArray();
    
    echo "Proceeding with deletion...\n";
    echo "The Product model's deleting event will automatically:\n";
    echo "1. Delete all color-size combinations\n";
    echo "2. Delete all color images from storage\n";
    echo "3. Delete all colors\n";
    echo "4. Delete all sizes\n";
    echo "5. Delete all specifications\n";
    echo "6. Delete the main product image\n";
    echo "7. Finally delete the product itself\n\n";
    
    // Perform the deletion
    $product->delete();
    
    echo "Product deleted successfully!\n\n";
    
    // Verify cascading deletion worked
    echo "Verification AFTER deletion:\n";
    
    // Check if product exists
    $productExists = Product::find($productId);
    echo "- Product (ID: {$productId}): " . ($productExists ? 'Still exists (ERROR!)' : 'Deleted ✓') . "\n";
    
    // Check colors
    $remainingColors = ProductColor::whereIn('id', $colorIds)->count();
    echo "- Colors: {$remainingColors} remaining (should be 0) " . ($remainingColors === 0 ? '✓' : '✗') . "\n";
    
    // Check sizes
    $remainingSizes = ProductSize::whereIn('id', $sizeIds)->count();
    echo "- Sizes: {$remainingSizes} remaining (should be 0) " . ($remainingSizes === 0 ? '✓' : '✗') . "\n";
    
    // Check color-size combinations
    $remainingColorSizes = ProductColorSize::whereIn('id', $colorSizeIds)->count();
    echo "- Color-Size combinations: {$remainingColorSizes} remaining (should be 0) " . ($remainingColorSizes === 0 ? '✓' : '✗') . "\n";
    
    // Check specifications
    $remainingSpecs = ProductSpecification::whereIn('id', $specificationIds)->count();
    echo "- Specifications: {$remainingSpecs} remaining (should be 0) " . ($remainingSpecs === 0 ? '✓' : '✗') . "\n";
    
    echo "\n=== Cascading Deletion Demonstration Complete ===\n";
    
    if ($remainingColors === 0 && $remainingSizes === 0 && $remainingColorSizes === 0 && $remainingSpecs === 0) {
        echo "✅ SUCCESS: All related records were properly deleted!\n";
    } else {
        echo "❌ ERROR: Some related records were not deleted properly!\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nNote: Check the Laravel logs for detailed deletion information.\n";
echo "Log location: storage/logs/laravel.log\n";
