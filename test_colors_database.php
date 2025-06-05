<?php

// Test script to check and seed product colors in the database

require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Facades\DB;

echo "=== Product Colors Database Test ===\n\n";

// 1. Check if product_colors table exists
echo "1. Checking if product_colors table exists...\n";
try {
    $tableExists = DB::getSchemaBuilder()->hasTable('product_colors');
    if ($tableExists) {
        echo "✅ product_colors table exists\n";
    } else {
        echo "❌ product_colors table does not exist\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Check current color count
echo "\n2. Checking current color count...\n";
$colorCount = ProductColor::count();
echo "Current colors in database: {$colorCount}\n";

// 3. Show sample colors if any exist
if ($colorCount > 0) {
    echo "\n3. Sample colors in database:\n";
    $sampleColors = ProductColor::take(10)->get(['id', 'name', 'color_code']);
    foreach ($sampleColors as $color) {
        echo "  - ID: {$color->id}, Name: {$color->name}, Code: {$color->color_code}\n";
    }
} else {
    echo "\n3. No colors found in database. Creating sample colors...\n";
    
    // Check if we have any products first
    $productCount = Product::count();
    echo "Products in database: {$productCount}\n";
    
    if ($productCount === 0) {
        echo "❌ No products found. Please run the product seeder first.\n";
        echo "Run: php artisan db:seed --class=ComprehensiveProductSeeder\n";
        exit(1);
    }
    
    // Get first few products to add colors to
    $products = Product::take(5)->get();
    
    $colorTemplates = [
        ['name' => 'Red', 'color_code' => '#FF0000'],
        ['name' => 'Blue', 'color_code' => '#0000FF'],
        ['name' => 'Green', 'color_code' => '#00FF00'],
        ['name' => 'Black', 'color_code' => '#000000'],
        ['name' => 'White', 'color_code' => '#FFFFFF'],
        ['name' => 'Yellow', 'color_code' => '#FFFF00'],
        ['name' => 'Purple', 'color_code' => '#800080'],
        ['name' => 'Orange', 'color_code' => '#FFA500'],
        ['name' => 'Pink', 'color_code' => '#FFC0CB'],
        ['name' => 'Brown', 'color_code' => '#A52A2A'],
    ];
    
    foreach ($products as $product) {
        // Add 3-4 random colors to each product
        $selectedColors = collect($colorTemplates)->random(rand(3, 4));
        
        foreach ($selectedColors as $index => $colorTemplate) {
            ProductColor::create([
                'product_id' => $product->id,
                'name' => $colorTemplate['name'],
                'color_code' => $colorTemplate['color_code'],
                'price_adjustment' => 0,
                'stock' => rand(5, 20),
                'display_order' => $index,
                'is_default' => $index === 0,
            ]);
        }
        
        echo "  ✅ Added colors to product: {$product->name}\n";
    }
    
    $newColorCount = ProductColor::count();
    echo "\n✅ Created {$newColorCount} colors in database\n";
}

// 4. Test the API endpoint
echo "\n4. Testing the API endpoint...\n";
try {
    // Simulate an HTTP request to the API endpoint
    $request = \Illuminate\Http\Request::create('/api/product-colors', 'GET');
    $controller = new \App\Http\Controllers\API\ProductSpecificationController();
    
    $response = $controller->getAllProductColors();
    $responseData = json_decode($response->getContent(), true);
    
    echo "API Response Status: " . $response->getStatusCode() . "\n";
    echo "API Response Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
    echo "API Response Color Count: " . ($responseData['count'] ?? 0) . "\n";
    
    if (isset($responseData['colors']) && count($responseData['colors']) > 0) {
        echo "\nSample colors from API:\n";
        foreach (array_slice($responseData['colors'], 0, 5) as $color) {
            echo "  - {$color['name']}: {$color['color_code']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error testing API: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
