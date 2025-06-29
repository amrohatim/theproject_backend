<?php

echo "🔍 Testing API Endpoint Directly\n";
echo "================================\n\n";

// Test the API endpoint that Flutter is trying to access
$apiUrl = 'http://192.168.70.48:8000/api/products?featured=true';

echo "Testing URL: $apiUrl\n\n";

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

// Execute the request
echo "📡 Making API request...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "📊 HTTP Status Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
    exit(1);
}

if ($httpCode === 200) {
    echo "✅ API request successful!\n\n";
    
    // Try to decode JSON response
    $data = json_decode($response, true);
    
    if ($data === null) {
        echo "❌ Invalid JSON response\n";
        echo "Raw response: " . substr($response, 0, 500) . "\n";
    } else {
        echo "📋 Response structure:\n";
        if (isset($data['success'])) {
            echo "  - Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        }
        
        if (isset($data['products'])) {
            if (isset($data['products']['data'])) {
                $products = $data['products']['data'];
                echo "  - Products count: " . count($products) . "\n";
                
                if (count($products) > 0) {
                    echo "  - First product: " . $products[0]['name'] . " (ID: " . $products[0]['id'] . ")\n";
                    echo "  - Featured: " . ($products[0]['featured'] ? 'true' : 'false') . "\n";
                }
            } else {
                echo "  - Products: " . count($data['products']) . "\n";
            }
        }
        
        echo "\n✅ API is working correctly!\n";
    }
} elseif ($httpCode === 500) {
    echo "❌ 500 Internal Server Error detected!\n\n";
    
    // Try to get more details from the response
    echo "📋 Error response:\n";
    echo substr($response, 0, 1000) . "\n";
    
    echo "\n🔧 Possible causes:\n";
    echo "1. Database connection issues\n";
    echo "2. Missing featured column in products table\n";
    echo "3. Laravel application error\n";
    echo "4. Missing dependencies or relationships\n";
    
} elseif ($httpCode === 0) {
    echo "❌ Could not connect to server\n";
    echo "🔧 Possible causes:\n";
    echo "1. Laravel server is not running\n";
    echo "2. Wrong IP address or port\n";
    echo "3. Firewall blocking connection\n";
    
} else {
    echo "❌ Unexpected HTTP status code: $httpCode\n";
    echo "Response: " . substr($response, 0, 500) . "\n";
}

echo "\n📋 Next steps:\n";
echo "1. If 500 error: Check Laravel logs in storage/logs/laravel.log\n";
echo "2. If connection error: Start Laravel server with 'php artisan serve --host=0.0.0.0 --port=8000'\n";
echo "3. If database error: Run migrations and seeders\n";
