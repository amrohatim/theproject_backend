<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Facades\Http;

echo "=== Comprehensive Image Fix Testing ===\n\n";

// Test 1: Verify all product color images are accessible via new routes
echo "1. Testing all product color images via new routes:\n";
$colors = ProductColor::whereNotNull('image')->get();
$successCount = 0;
$failCount = 0;

foreach ($colors as $color) {
    $imageUrl = $color->image;
    $filename = basename($color->getRawImagePath());
    
    echo "Testing: {$filename}\n";
    echo "URL: {$imageUrl}\n";
    
    try {
        $response = Http::timeout(10)->get($imageUrl);
        
        if ($response->successful()) {
            $contentType = $response->header('Content-Type');
            echo "  ✅ SUCCESS - Status: {$response->status()}, Type: {$contentType}\n";
            $successCount++;
        } else {
            echo "  ❌ FAILED - Status: {$response->status()}\n";
            $failCount++;
        }
    } catch (\Exception $e) {
        echo "  ❌ ERROR - " . $e->getMessage() . "\n";
        $failCount++;
    }
    
    echo "\n";
}

echo "Summary: {$successCount} successful, {$failCount} failed\n\n";

// Test 2: Test specific problematic images
echo "2. Testing specific problematic images:\n";
$problematicImages = [
    '1751640943_uBysKFaIUZ.png',
    '1751640333_gR36eTVlBM.png',
    '1751674751_KM0JQfIugP.png',
    '1751674928_dVL8IcWFx3.png'
];

foreach ($problematicImages as $filename) {
    $url = route('images.products.colors', ['filename' => $filename]);
    echo "Testing: {$filename}\n";
    echo "URL: {$url}\n";
    
    try {
        $response = Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $contentType = $response->header('Content-Type');
            $contentLength = $response->header('Content-Length');
            echo "  ✅ SUCCESS - Status: {$response->status()}, Type: {$contentType}, Size: {$contentLength} bytes\n";
        } else {
            echo "  ❌ FAILED - Status: {$response->status()}\n";
        }
    } catch (\Exception $e) {
        echo "  ❌ ERROR - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test 3: Verify route generation consistency
echo "3. Testing route generation consistency:\n";
foreach ($colors as $color) {
    $rawPath = $color->getRawImagePath();
    $processedUrl = $color->image;
    $filename = basename($rawPath);
    
    // Test both old and new route methods
    $oldRoute = route('images.products', ['filename' => $filename]);
    $newRoute = route('images.products.colors', ['filename' => $filename]);
    
    echo "Color: {$color->name}\n";
    echo "  Raw path: {$rawPath}\n";
    echo "  Processed URL: {$processedUrl}\n";
    echo "  Old route: {$oldRoute}\n";
    echo "  New route: {$newRoute}\n";
    echo "  Using correct route: " . ($processedUrl === $newRoute ? 'Yes' : 'No') . "\n\n";
}

// Test 4: File system verification
echo "4. File system verification:\n";
$storageDir = storage_path('app/public/products/colors');
$publicDir = public_path('storage/products/colors');

echo "Storage directory: {$storageDir}\n";
echo "Public directory: {$publicDir}\n";
echo "Storage dir exists: " . (is_dir($storageDir) ? 'Yes' : 'No') . "\n";
echo "Public dir exists: " . (is_dir($publicDir) ? 'Yes' : 'No') . "\n";

if (is_dir($storageDir)) {
    $storageFiles = glob($storageDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    echo "Files in storage: " . count($storageFiles) . "\n";
}

if (is_dir($publicDir)) {
    $publicFiles = glob($publicDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    echo "Files in public: " . count($publicFiles) . "\n";
}

echo "\n=== Test Complete ===\n";
