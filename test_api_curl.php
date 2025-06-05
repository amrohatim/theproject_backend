<?php

echo "🔍 Testing Featured Products API with cURL\n";
echo "==========================================\n\n";

// Test different variations of the API endpoint
$endpoints = [
    'http://127.0.0.1:8000/api/products?featured=true',
    'http://localhost:8000/api/products?featured=true', 
    'http://192.168.70.48:8000/api/products?featured=true',
    'http://127.0.0.1:8000/api/featured/products', // Alternative endpoint
];

foreach ($endpoints as $index => $url) {
    echo ($index + 1) . ". Testing: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'User-Agent: FeaturedProductsTest/1.0'
    ]);
    
    $startTime = microtime(true);
    $response = curl_exec($ch);
    $endTime = microtime(true);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    $responseTime = round(($endTime - $startTime) * 1000, 2);
    
    echo "   📊 HTTP Status: $httpCode\n";
    echo "   ⏱️  Response Time: {$responseTime}ms\n";
    
    if ($error) {
        echo "   ❌ cURL Error: $error\n";
        echo "   🔧 Server not running or not accessible\n";
    } else {
        switch ($httpCode) {
            case 200:
                echo "   ✅ Success!\n";
                
                $data = json_decode($response, true);
                if ($data === null) {
                    echo "   ❌ Invalid JSON response\n";
                    echo "   📋 Raw response (first 200 chars): " . substr($response, 0, 200) . "\n";
                } else {
                    echo "   📋 Response structure:\n";
                    
                    if (isset($data['success'])) {
                        echo "      - Success: " . ($data['success'] ? 'true' : 'false') . "\n";
                    }
                    
                    if (isset($data['products'])) {
                        if (is_array($data['products']) && isset($data['products']['data'])) {
                            // Paginated response
                            $products = $data['products']['data'];
                            echo "      - Products (paginated): " . count($products) . "\n";
                            echo "      - Total: " . ($data['products']['total'] ?? 'unknown') . "\n";
                        } elseif (is_array($data['products'])) {
                            // Direct array response
                            echo "      - Products (direct): " . count($data['products']) . "\n";
                            $products = $data['products'];
                        } else {
                            echo "      - Products: Invalid format\n";
                            $products = [];
                        }
                        
                        if (!empty($products)) {
                            $firstProduct = $products[0];
                            echo "      - Sample product:\n";
                            echo "         * ID: " . ($firstProduct['id'] ?? 'unknown') . "\n";
                            echo "         * Name: " . ($firstProduct['name'] ?? 'unknown') . "\n";
                            echo "         * Featured: " . (isset($firstProduct['featured']) ? ($firstProduct['featured'] ? 'true' : 'false') : 'unknown') . "\n";
                        }
                    }
                }
                break;
                
            case 500:
                echo "   ❌ 500 Internal Server Error - This is the problem!\n";
                
                // Try to extract error details
                if (strpos($response, 'Whoops') !== false) {
                    echo "   📋 Laravel error page detected\n";
                    
                    // Try to extract the main error message
                    if (preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $response, $matches)) {
                        echo "   📋 Error: " . strip_tags($matches[1]) . "\n";
                    }
                } else {
                    echo "   📋 Raw error (first 300 chars):\n";
                    echo "   " . substr($response, 0, 300) . "\n";
                }
                break;
                
            case 404:
                echo "   ❌ 404 Not Found - Route not registered\n";
                break;
                
            case 0:
                echo "   ❌ Connection failed - Server not running\n";
                break;
                
            default:
                echo "   ❌ Unexpected status: $httpCode\n";
                break;
        }
    }
    
    echo "\n";
}

echo "📋 Summary:\n";
echo "==========\n";
echo "If you see 500 errors above, run: php debug_500_error.php\n";
echo "If you see connection errors, start Laravel server:\n";
echo "   php artisan serve --host=0.0.0.0 --port=8000\n\n";
