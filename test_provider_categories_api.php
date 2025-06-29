<?php
// Script to test the provider categories API endpoints

// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\API\ProviderController;

echo "Testing Provider Categories API endpoints...\n\n";

try {
    $controller = new ProviderController();

    // Test 1: Get categories with products
    echo "=== Test 1: Get Categories with Products ===\n";
    $response = $controller->getCategoriesWithProducts();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ Success! Found " . count($data['categories']) . " subcategories with products:\n";
        foreach ($data['categories'] as $category) {
            echo "  - {$category['name']} (ID: {$category['id']}) - Parent: {$category['parent_name']} - Products: {$category['product_count']}\n";
        }
    } else {
        echo "❌ Failed: " . $data['message'] . "\n";
    }

    echo "\n";

    // Test 2: Get products by category for the first subcategory
    if (!empty($data['categories'])) {
        $firstCategory = $data['categories'][0];
        echo "=== Test 2: Get Products for Subcategory '{$firstCategory['name']}' (ID: {$firstCategory['id']}) ===\n";
        
        $request = new Request(['include_subcategories' => false]);
        $response = $controller->getProductsByCategory($firstCategory['id'], $request);
        $productData = json_decode($response->getContent(), true);
        
        if ($productData['success']) {
            $products = $productData['products']['data'] ?? $productData['products'];
            echo "✅ Success! Found " . count($products) . " products:\n";
            foreach ($products as $product) {
                echo "  - {$product['name']} (ID: {$product['id']}) - Price: \${$product['price']}\n";
            }
        } else {
            echo "❌ Failed: " . $productData['message'] . "\n";
        }
    }

    echo "\n";

    // Test 3: Check provider_products table directly
    echo "=== Test 3: Direct Database Check ===\n";
    $providerProducts = \Illuminate\Support\Facades\DB::select("
        SELECT pp.id, pp.product_name, pp.category_id, c.name as category_name, c.parent_id
        FROM provider_products pp
        LEFT JOIN categories c ON pp.category_id = c.id
        WHERE pp.category_id IS NOT NULL
        LIMIT 5
    ");

    echo "Provider products with categories:\n";
    foreach ($providerProducts as $product) {
        echo "  - {$product->product_name} (Category: {$product->category_name}, ID: {$product->category_id})\n";
    }

    echo "\n✅ API testing completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
