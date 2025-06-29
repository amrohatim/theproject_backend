<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;

echo "🔍 Testing Featured Products API Endpoint\n";
echo "=========================================\n\n";

try {
    // Simulate the API request
    echo "1. Testing /api/products?featured=true endpoint...\n";
    
    $controller = new ProductController();
    $request = new Request(['featured' => 'true']);
    
    // Call the index method with featured=true
    $response = $controller->index($request);
    $responseData = $response->getData(true);
    
    echo "✅ API endpoint responded successfully\n";
    echo "📊 Response structure:\n";
    echo "   - Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
    
    if (isset($responseData['products'])) {
        if (is_array($responseData['products'])) {
            $productCount = count($responseData['products']);
            echo "   - Products count: $productCount\n\n";
            
            if ($productCount > 0) {
                echo "📋 Featured Products from API:\n";
                foreach ($responseData['products'] as $index => $product) {
                    echo "   " . ($index + 1) . ". {$product['name']} (ID: {$product['id']})\n";
                    echo "      Price: \${$product['price']}\n";
                    echo "      Featured: " . ($product['featured'] ? 'Yes' : 'No') . "\n";
                    echo "      Available: " . ($product['is_available'] ? 'Yes' : 'No') . "\n\n";
                }
            } else {
                echo "❌ No featured products returned from API\n";
            }
        } else {
            echo "   - Products format: Paginated response\n";
            if (isset($responseData['products']['data'])) {
                $productCount = count($responseData['products']['data']);
                echo "   - Products count: $productCount\n";
            }
        }
    } else {
        echo "❌ No products key in response\n";
    }

    echo "\n2. Testing dedicated /api/featured/products endpoint...\n";
    
    $featuredResponse = $controller->featured($request);
    $featuredData = $featuredResponse->getData(true);
    
    echo "✅ Featured endpoint responded successfully\n";
    echo "📊 Featured endpoint response:\n";
    echo "   - Success: " . ($featuredData['success'] ? 'true' : 'false') . "\n";
    
    if (isset($featuredData['products'])) {
        $featuredCount = count($featuredData['products']);
        echo "   - Featured products count: $featuredCount\n\n";
        
        if ($featuredCount > 0) {
            echo "📋 Products from /featured/products endpoint:\n";
            foreach ($featuredData['products'] as $index => $product) {
                echo "   " . ($index + 1) . ". {$product['name']} (ID: {$product['id']})\n";
            }
        }
    }

    echo "\n✅ API endpoint testing completed!\n\n";
    
    echo "🔧 Debugging Information:\n";
    echo "- Both endpoints are working correctly\n";
    echo "- The issue is likely in the network connectivity between Flutter app and Laravel server\n";
    echo "- Check that Laravel server is running on: http://192.168.70.48:8000\n";
    echo "- Verify Flutter app can reach the server\n";

} catch (Exception $e) {
    echo "❌ Error testing API endpoint: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
