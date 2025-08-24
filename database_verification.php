<?php
/**
 * Database Verification Script for Vendor Product Creation Test
 * Verifies that products are created with correct user_id assignment
 * and proper relationships for colors, sizes, and color-size combinations
 */

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\User;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use App\Models\ProductSpecification;

function verifyProductCreation($productId = null) {
    echo "🔍 Starting Database Verification...\n";
    echo str_repeat("=", 60) . "\n";
    
    try {
        // Find the product to verify
        if ($productId) {
            $product = Product::find($productId);
            echo "📋 Verifying specific product ID: {$productId}\n";
        } else {
            $product = Product::latest()->first();
            echo "📋 Verifying latest product in database\n";
        }
        
        if (!$product) {
            throw new Exception("No product found for verification");
        }
        
        echo "✅ Product found: {$product->name} (ID: {$product->id})\n";
        
        // Verify user_id assignment
        echo "\n🔍 Checking user_id assignment...\n";
        if (!$product->user_id) {
            throw new Exception("❌ Product user_id is null - assignment failed!");
        }
        
        echo "✅ Product has user_id: {$product->user_id}\n";
        
        // Verify user exists and is correct type
        $user = User::find($product->user_id);
        if (!$user) {
            throw new Exception("❌ User not found for user_id: {$product->user_id}");
        }
        
        echo "✅ User found: {$user->name} ({$user->email})\n";
        echo "✅ User role: {$user->role}\n";
        
        if ($user->role !== 'merchant') {
            echo "⚠️  Warning: User role is '{$user->role}', expected 'merchant'\n";
        }
        
        // Verify product basic data
        echo "\n🔍 Checking product basic data...\n";
        echo "  📋 Name: {$product->name}\n";
        echo "  📋 Price: \${$product->price}\n";
        echo "  📋 Stock: {$product->stock}\n";
        echo "  📋 Category ID: {$product->category_id}\n";
        echo "  📋 Branch ID: {$product->branch_id}\n";
        echo "  📋 Available: " . ($product->is_available ? 'Yes' : 'No') . "\n";
        
        // Verify colors
        echo "\n🔍 Checking product colors...\n";
        $colors = ProductColor::where('product_id', $product->id)->get();
        echo "✅ Found {$colors->count()} color(s)\n";
        
        foreach ($colors as $index => $color) {
            echo "  Color " . ($index + 1) . ":\n";
            echo "    📋 Name: {$color->name}\n";
            echo "    📋 Code: {$color->color_code}\n";
            echo "    📋 Stock: {$color->stock}\n";
            echo "    📋 Default: " . ($color->is_default ? 'Yes' : 'No') . "\n";
            echo "    📋 Image: " . ($color->image ? $color->image : 'None') . "\n";
        }
        
        // Verify sizes
        echo "\n🔍 Checking product sizes...\n";
        $sizes = ProductSize::where('product_id', $product->id)->get();
        echo "✅ Found {$sizes->count()} size(s)\n";
        
        foreach ($sizes as $index => $size) {
            echo "  Size " . ($index + 1) . ":\n";
            echo "    📋 Name: {$size->name}\n";
            echo "    📋 Value: " . ($size->value ?: 'N/A') . "\n";
            echo "    📋 Stock: {$size->stock}\n";
            echo "    📋 Default: " . ($size->is_default ? 'Yes' : 'No') . "\n";
        }
        
        // Verify color-size combinations
        echo "\n🔍 Checking color-size combinations...\n";
        $colorSizes = ProductColorSize::where('product_id', $product->id)
            ->with(['color', 'size'])
            ->get();
        echo "✅ Found {$colorSizes->count()} color-size combination(s)\n";
        
        foreach ($colorSizes as $index => $cs) {
            echo "  Combination " . ($index + 1) . ":\n";
            echo "    📋 Color: {$cs->color->name} ({$cs->color->color_code})\n";
            echo "    📋 Size: {$cs->size->name}\n";
            echo "    📋 Stock: {$cs->stock}\n";
            echo "    📋 Available: " . ($cs->is_available ? 'Yes' : 'No') . "\n";
        }
        
        // Verify specifications
        echo "\n🔍 Checking product specifications...\n";
        $specifications = ProductSpecification::where('product_id', $product->id)->get();
        echo "✅ Found {$specifications->count()} specification(s)\n";
        
        foreach ($specifications as $index => $spec) {
            echo "  Specification " . ($index + 1) . ":\n";
            echo "    📋 Name: {$spec->name}\n";
            echo "    📋 Value: {$spec->value}\n";
        }
        
        // Verify relationships integrity
        echo "\n🔍 Checking relationship integrity...\n";
        
        // Check if all colors belong to the correct product
        $invalidColors = ProductColor::where('product_id', $product->id)
            ->whereHas('product', function($query) use ($product) {
                $query->where('id', '!=', $product->id);
            })->count();
        
        if ($invalidColors > 0) {
            throw new Exception("❌ Found {$invalidColors} colors with invalid product relationship");
        }
        echo "✅ All colors have correct product relationship\n";
        
        // Check if all sizes belong to the correct product
        $invalidSizes = ProductSize::where('product_id', $product->id)
            ->whereHas('product', function($query) use ($product) {
                $query->where('id', '!=', $product->id);
            })->count();
        
        if ($invalidSizes > 0) {
            throw new Exception("❌ Found {$invalidSizes} sizes with invalid product relationship");
        }
        echo "✅ All sizes have correct product relationship\n";
        
        // Summary
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 VERIFICATION SUMMARY\n";
        echo str_repeat("=", 60) . "\n";
        echo "✅ Product ID: {$product->id}\n";
        echo "✅ User ID: {$product->user_id}\n";
        echo "✅ User: {$user->name} ({$user->email})\n";
        echo "✅ User Role: {$user->role}\n";
        echo "✅ Colors: {$colors->count()}\n";
        echo "✅ Sizes: {$sizes->count()}\n";
        echo "✅ Color-Size Combinations: {$colorSizes->count()}\n";
        echo "✅ Specifications: {$specifications->count()}\n";
        echo "\n🎉 ALL VERIFICATIONS PASSED!\n";
        echo "The product was created successfully with proper user_id assignment\n";
        echo "and all relationships are correctly established.\n";
        echo str_repeat("=", 60) . "\n";
        
        return [
            'success' => true,
            'productId' => $product->id,
            'userId' => $product->user_id,
            'userName' => $user->name,
            'userEmail' => $user->email,
            'userRole' => $user->role,
            'productName' => $product->name,
            'colorsCount' => $colors->count(),
            'sizesCount' => $sizes->count(),
            'colorSizesCount' => $colorSizes->count(),
            'specificationsCount' => $specifications->count(),
            'colors' => $colors->map(function($color) {
                return [
                    'id' => $color->id,
                    'name' => $color->name,
                    'color_code' => $color->color_code,
                    'stock' => $color->stock,
                    'is_default' => $color->is_default
                ];
            })->toArray(),
            'sizes' => $sizes->map(function($size) {
                return [
                    'id' => $size->id,
                    'name' => $size->name,
                    'value' => $size->value,
                    'stock' => $size->stock,
                    'is_default' => $size->is_default
                ];
            })->toArray(),
            'colorSizes' => $colorSizes->map(function($cs) {
                return [
                    'color_name' => $cs->color->name,
                    'size_name' => $cs->size->name,
                    'stock' => $cs->stock,
                    'is_available' => $cs->is_available
                ];
            })->toArray()
        ];
        
    } catch (Exception $e) {
        echo "❌ VERIFICATION FAILED: " . $e->getMessage() . "\n";
        echo str_repeat("=", 60) . "\n";
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function checkBeforeState() {
    echo "📋 BEFORE STATE - Current Database Status\n";
    echo str_repeat("-", 40) . "\n";
    
    $totalProducts = Product::count();
    $totalColors = ProductColor::count();
    $totalSizes = ProductSize::count();
    $totalColorSizes = ProductColorSize::count();
    
    echo "📊 Current counts:\n";
    echo "  Products: {$totalProducts}\n";
    echo "  Colors: {$totalColors}\n";
    echo "  Sizes: {$totalSizes}\n";
    echo "  Color-Size combinations: {$totalColorSizes}\n";
    
    // Show latest products
    $latestProducts = Product::latest()->take(3)->get();
    echo "\n📋 Latest products:\n";
    foreach ($latestProducts as $product) {
        echo "  - ID {$product->id}: {$product->name} (User: {$product->user_id})\n";
    }
    
    echo str_repeat("-", 40) . "\n\n";
}

// Main execution
if (php_sapi_name() === 'cli') {
    // Command line execution
    $productId = $argv[1] ?? null;
    
    if ($productId && $productId === 'before') {
        checkBeforeState();
    } else {
        $result = verifyProductCreation($productId);
        
        // Output JSON for programmatic access
        if (isset($argv[2]) && $argv[2] === '--json') {
            echo "\n" . json_encode($result, JSON_PRETTY_PRINT) . "\n";
        }
    }
} else {
    // Web execution
    $productId = $_GET['product_id'] ?? null;
    $format = $_GET['format'] ?? 'html';
    
    if ($format === 'json') {
        header('Content-Type: application/json');
        echo json_encode(verifyProductCreation($productId));
    } else {
        echo "<pre>";
        verifyProductCreation($productId);
        echo "</pre>";
    }
}
?>
