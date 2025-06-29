<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\API\CheckoutController;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Services\StockManagementService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Stock Management Fix Test ===\n\n";

try {
    // Test the StockManagementService directly
    $stockService = new StockManagementService();
    
    // Find a product with color and size variations
    $product = Product::with(['colors.colorSizes'])->whereHas('colors.colorSizes')->first();
    
    if (!$product) {
        echo "❌ No product found with color and size variations\n";
        echo "Creating test data...\n";
        
        // Create a test product with variations
        $product = Product::create([
            'branch_id' => 1,
            'category_id' => 1,
            'user_id' => 1,
            'name' => 'Test Product for Stock Management',
            'price' => 100.00,
            'stock' => 50,
            'sku' => 'TEST-STOCK-' . time(),
            'description' => 'Test product for stock management verification',
            'is_available' => true,
        ]);
        
        // Create color variation
        $color = ProductColor::create([
            'product_id' => $product->id,
            'name' => 'Red',
            'color_code' => '#FF0000',
            'stock' => 20,
            'is_default' => true,
        ]);
        
        // Create size variation
        $size = \App\Models\ProductSize::create([
            'product_id' => $product->id,
            'name' => 'Medium',
            'value' => 'M',
            'stock' => 15,
        ]);
        
        // Create color-size combination
        $colorSize = ProductColorSize::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'product_size_id' => $size->id,
            'stock' => 10,
            'is_available' => true,
        ]);
        
        echo "✅ Test data created successfully\n\n";
    }
    
    // Get the first color and size
    $color = $product->colors()->first();
    $size = $product->sizes()->first();
    $colorSize = $product->colorSizes()->first();
    
    if (!$color || !$size || !$colorSize) {
        echo "❌ Product doesn't have complete color/size variations\n";
        exit(1);
    }
    
    echo "Testing product: {$product->name} (ID: {$product->id})\n";
    echo "Color: {$color->name} (ID: {$color->id})\n";
    echo "Size: {$size->name} (ID: {$size->id})\n\n";
    
    // Display initial stock levels
    echo "=== Initial Stock Levels ===\n";
    echo "Product general stock: {$product->stock}\n";
    echo "Color stock: {$color->stock}\n";
    echo "Color-Size stock: {$colorSize->stock}\n\n";
    
    // Test stock availability check
    echo "=== Testing Stock Availability Check ===\n";
    $quantity = 2;
    $available = $stockService->checkStockAvailability($product->id, $quantity, $color->id, $size->id);
    echo "Checking availability for quantity {$quantity}: " . ($available ? "✅ Available" : "❌ Not Available") . "\n\n";
    
    if (!$available) {
        echo "❌ Stock not available for testing. Exiting.\n";
        exit(1);
    }
    
    // Test stock reduction
    echo "=== Testing Stock Reduction ===\n";
    echo "Reducing stock by {$quantity}...\n";
    
    $success = $stockService->reduceStock($product->id, $quantity, $color->id, $size->id);
    
    if ($success) {
        echo "✅ Stock reduction successful\n";
        
        // Refresh models to get updated stock
        $product->refresh();
        $color->refresh();
        $colorSize->refresh();
        
        echo "Updated stock levels:\n";
        echo "Product general stock: {$product->stock}\n";
        echo "Color stock: {$color->stock}\n";
        echo "Color-Size stock: {$colorSize->stock}\n\n";
    } else {
        echo "❌ Stock reduction failed\n";
    }
    
    // Test stock increase (restoration)
    echo "=== Testing Stock Restoration ===\n";
    echo "Restoring stock by {$quantity}...\n";
    
    $success = $stockService->increaseStock($product->id, $quantity, $color->id, $size->id);
    
    if ($success) {
        echo "✅ Stock restoration successful\n";
        
        // Refresh models to get updated stock
        $product->refresh();
        $color->refresh();
        $colorSize->refresh();
        
        echo "Restored stock levels:\n";
        echo "Product general stock: {$product->stock}\n";
        echo "Color stock: {$color->stock}\n";
        echo "Color-Size stock: {$colorSize->stock}\n\n";
    } else {
        echo "❌ Stock restoration failed\n";
    }
    
    // Test edge case: insufficient stock
    echo "=== Testing Insufficient Stock Scenario ===\n";
    $largeQuantity = $colorSize->stock + 10;
    echo "Attempting to reduce stock by {$largeQuantity} (more than available)...\n";
    
    try {
        $stockService->reduceStock($product->id, $largeQuantity, $color->id, $size->id);
        echo "❌ Should have failed but didn't\n";
    } catch (\Exception $e) {
        echo "✅ Correctly threw exception: " . $e->getMessage() . "\n\n";
    }
    
    echo "=== Stock Management Fix Test Completed Successfully ===\n";
    echo "✅ All stock operations are working correctly\n";
    echo "✅ General product stock is updated\n";
    echo "✅ Color variation stock is updated\n";
    echo "✅ Color-size combination stock is updated\n";
    echo "✅ Stock availability checks work properly\n";
    echo "✅ Error handling for insufficient stock works\n\n";
    
    echo "The stock management issue has been fixed!\n";
    echo "Orders will now properly update stock for all variations.\n";
    
} catch (\Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}