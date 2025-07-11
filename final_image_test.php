<?php

require_once 'vendor/autoload.php';

use App\Models\Product;
use App\Helpers\ImageHelper;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "FINAL IMAGE VERIFICATION TEST\n";
echo "============================\n\n";

// 1. Check storage link
echo "1. STORAGE LINK STATUS:\n";
$publicStoragePath = public_path('storage');
if (is_link($publicStoragePath)) {
    echo "✓ Storage link exists and is a symbolic link\n";
    echo "  Target: " . readlink($publicStoragePath) . "\n";
} else if (is_dir($publicStoragePath)) {
    echo "✗ Storage path exists but is a directory (not a symbolic link)\n";
} else {
    echo "✗ Storage link does not exist\n";
}

// 2. Test specific products
echo "\n2. TESTING SPECIFIC PRODUCTS:\n";

// Test the original product with SKU AMROOSMA9CY
$originalProduct = Product::where('sku', 'AMROOSMA9CY')->first();
if ($originalProduct) {
    echo "✓ Original product found (SKU: AMROOSMA9CY, ID: {$originalProduct->id})\n";
    echo "  Raw image path: " . ($originalProduct->getRawOriginal('image') ?: 'NULL') . "\n";
    echo "  Processed image path: " . ($originalProduct->image ?: 'NULL') . "\n";
    
    // Check if file exists
    $rawPath = $originalProduct->getRawOriginal('image');
    if ($rawPath) {
        $fullPath = storage_path('app/public/' . $rawPath);
        $publicPath = public_path('storage/' . $rawPath);
        echo "  Storage file exists: " . (file_exists($fullPath) ? "✓" : "✗") . "\n";
        echo "  Public file exists: " . (file_exists($publicPath) ? "✓" : "✗") . "\n";
        
        if (file_exists($publicPath)) {
            echo "  File size: " . filesize($publicPath) . " bytes\n";
        }
    }
} else {
    echo "✗ Original product (SKU: AMROOSMA9CY) not found\n";
}

echo "\n";

// Test the newly created product
$newProduct = Product::where('sku', 'TESTPROD2RN')->first();
if ($newProduct) {
    echo "✓ New test product found (SKU: TESTPROD2RN, ID: {$newProduct->id})\n";
    echo "  Raw image path: " . ($newProduct->getRawOriginal('image') ?: 'NULL') . "\n";
    echo "  Processed image path: " . ($newProduct->image ?: 'NULL') . "\n";
    
    // Check if file exists
    $rawPath = $newProduct->getRawOriginal('image');
    if ($rawPath) {
        $fullPath = storage_path('app/public/' . $rawPath);
        $publicPath = public_path('storage/' . $rawPath);
        echo "  Storage file exists: " . (file_exists($fullPath) ? "✓" : "✗") . "\n";
        echo "  Public file exists: " . (file_exists($publicPath) ? "✓" : "✗") . "\n";
        
        if (file_exists($publicPath)) {
            echo "  File size: " . filesize($publicPath) . " bytes\n";
        }
    }
} else {
    echo "✗ New test product (SKU: TESTPROD2RN) not found\n";
}

// 3. Test ImageHelper functionality
echo "\n3. TESTING IMAGEHELPER:\n";
$testPaths = [
    'products/test.jpg',
    'products/1751433487_Sg3zbLltVN.png',
    null,
    '',
];

foreach ($testPaths as $testPath) {
    $result = ImageHelper::getImagePath($testPath);
    echo "  Input: " . ($testPath ?: 'NULL') . " -> Output: " . ($result ?: 'NULL') . "\n";
}

// 4. Count total products with images
echo "\n4. PRODUCT IMAGE STATISTICS:\n";
$totalProducts = Product::count();
$productsWithImages = Product::whereNotNull('image')->where('image', '!=', '')->count();
$productsWithoutImages = $totalProducts - $productsWithImages;

echo "  Total products: $totalProducts\n";
echo "  Products with images: $productsWithImages\n";
echo "  Products without images: $productsWithoutImages\n";

// 5. Check recent uploads
echo "\n5. RECENT IMAGE UPLOADS:\n";
$recentProducts = Product::whereNotNull('image')
    ->where('image', '!=', '')
    ->where('created_at', '>=', now()->subHours(2))
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

if ($recentProducts->count() > 0) {
    foreach ($recentProducts as $product) {
        echo "  Product ID {$product->id} ({$product->sku}): {$product->getRawOriginal('image')}\n";
        $publicPath = public_path('storage/' . $product->getRawOriginal('image'));
        echo "    File exists: " . (file_exists($publicPath) ? "✓" : "✗") . "\n";
    }
} else {
    echo "  No recent uploads found\n";
}

echo "\nTEST COMPLETE\n";
