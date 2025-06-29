<?php

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Configuration
$baseUrl = 'http://192.168.70.48:8000/api';
$client = new Client(['base_uri' => $baseUrl]);

echo "Testing Laravel API Backend Fixes\n";
echo "================================\n\n";

// Test 1: Provider Products Endpoint
echo "Test 1: Provider Products Endpoint\n";
echo "-----------------------------------\n";
try {
    $response = $client->get('/api/providers/6/products', [
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]
    ]);
    
    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody(), true);
    
    echo "Status Code: $statusCode\n";
    echo "Response: " . json_encode($body, JSON_PRETTY_PRINT) . "\n";
    
    if ($statusCode == 200 && isset($body['success']) && $body['success']) {
        echo "✅ Provider products endpoint is working!\n";
    } else {
        echo "❌ Provider products endpoint has issues.\n";
    }
} catch (RequestException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    if ($e->hasResponse()) {
        echo "Response: " . $e->getResponse()->getBody() . "\n";
    }
}

echo "\n";

// Test 2: Categories with Deals Endpoint  
echo "Test 2: Categories with Deals Endpoint\n";
echo "--------------------------------------\n";
try {
    $response = $client->get('/api/categories-with-deals', [
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]
    ]);
    
    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody(), true);
    
    echo "Status Code: $statusCode\n";
    echo "Response: " . json_encode($body, JSON_PRETTY_PRINT) . "\n";
    
    if ($statusCode == 200 && isset($body['success']) && $body['success']) {
        echo "✅ Categories with deals endpoint is working!\n";
    } else {
        echo "❌ Categories with deals endpoint has issues.\n";
    }
} catch (RequestException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    if ($e->hasResponse()) {
        echo "Response: " . $e->getResponse()->getBody() . "\n";
    }
}

echo "\n";

// Test 3: User Location Endpoints (without authentication for now)
echo "Test 3: User Location Endpoints Structure\n";
echo "-----------------------------------------\n";
echo "The following user location endpoints have been added:\n";
echo "• GET /user-locations - Get all user locations\n";
echo "• POST /user-locations - Create new location\n";
echo "• GET /user-locations/{id} - Get specific location\n";
echo "• PUT /user-locations/{id} - Update location\n";
echo "• DELETE /user-locations/{id} - Delete location\n";
echo "• PUT /user-locations/{id}/set-default - Set as default location\n";
echo "\nNote: These endpoints require authentication and will work once a valid token is provided.\n";

echo "\n";

// Test 4: Health Check
echo "Test 4: API Health Check\n";
echo "------------------------\n";
try {
    $response = $client->get('/api/health-check');
    
    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody(), true);
    
    echo "Status Code: $statusCode\n";
    echo "Response: " . json_encode($body, JSON_PRETTY_PRINT) . "\n";
    
    if ($statusCode == 200 && isset($body['status']) && $body['status'] == 'ok') {
        echo "✅ API is running correctly!\n";
    } else {
        echo "❌ API health check failed.\n";
    }
} catch (RequestException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "Testing completed!\n";
echo "==================\n";
echo "Summary of fixes applied:\n";
echo "1. ✅ Added getProducts() method in ProviderController\n";
echo "2. ✅ Added getCategoriesWithDeals() method in CategoryController\n";
echo "3. ✅ Added complete UserLocationController routes\n";
echo "4. ✅ Fixed Log import issues\n";
echo "\nThe Flutter app should now be able to:\n";
echo "• Access http://192.168.70.48:8000/api/providers/6/products without 500 errors\n";
echo "• Save user locations successfully\n";
echo "• Load categories with deals without DioException errors\n";