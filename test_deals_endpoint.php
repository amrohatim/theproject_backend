<?php

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Configuration
$baseUrl = 'http://localhost:8000/api';
$client = new Client([
    'timeout' => 10,
    'verify' => false,
]);

echo "=== DEALS ENDPOINT TEST ===\n";
echo "Testing the /active-deals endpoint that was missing...\n\n";

// Test 1: Check if /active-deals endpoint exists and responds
echo "1. Testing /active-deals endpoint (the one that was missing):\n";
echo "   GET $baseUrl/active-deals\n";

try {
    $response = $client->get("$baseUrl/active-deals", [
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]
    ]);
    
    $statusCode = $response->getStatusCode();
    $data = json_decode($response->getBody(), true);
    
    echo "   ✅ Status: $statusCode\n";
    echo "   ✅ Response structure: " . (isset($data['success']) ? 'Valid' : 'Invalid') . "\n";
    
    if (isset($data['success']) && $data['success']) {
        $dealCount = isset($data['deals']) ? count($data['deals']) : 0;
        echo "   ✅ Deals found: $dealCount\n";
        
        if ($dealCount > 0) {
            echo "   📋 Sample deal data:\n";
            $firstDeal = $data['deals'][0];
            echo "      - ID: " . ($firstDeal['id'] ?? 'N/A') . "\n";
            echo "      - Title: " . ($firstDeal['title'] ?? 'N/A') . "\n";
            echo "      - Status: " . ($firstDeal['status'] ?? 'N/A') . "\n";
            echo "      - Discount: " . ($firstDeal['discount_percentage'] ?? 'N/A') . "%\n";
        }
    } else {
        echo "   ⚠️  API returned success=false: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    
} catch (RequestException $e) {
    $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 'No response';
    echo "   ❌ Error: HTTP $statusCode\n";
    
    if ($e->getResponse()) {
        $errorBody = $e->getResponse()->getBody()->getContents();
        $errorData = json_decode($errorBody, true);
        
        if ($statusCode == 401) {
            echo "   🔑 Authentication required - this is expected for protected endpoints\n";
            echo "   📝 This means the endpoint exists but requires authentication\n";
        } else {
            echo "   📄 Error details: " . ($errorData['message'] ?? $errorBody) . "\n";
        }
    } else {
        echo "   📄 Connection error: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Test 2: Test with authentication (if we can get a token)
echo "2. Testing with authentication:\n";

// First, try to login to get a token
echo "   Attempting to get authentication token...\n";

try {
    $loginResponse = $client->post("$baseUrl/login", [
        'json' => [
            'email' => 'admin@example.com',
            'password' => 'password'
        ],
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]
    ]);
    
    $loginData = json_decode($loginResponse->getBody(), true);
    
    if (isset($loginData['success']) && $loginData['success'] && isset($loginData['token'])) {
        $token = $loginData['token'];
        echo "   ✅ Login successful, got token\n";
        
        // Now test the active-deals endpoint with authentication
        echo "   Testing /active-deals with authentication...\n";
        
        $authResponse = $client->get("$baseUrl/active-deals", [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer $token",
            ]
        ]);
        
        $authData = json_decode($authResponse->getBody(), true);
        $statusCode = $authResponse->getStatusCode();
        
        echo "   ✅ Authenticated request status: $statusCode\n";
        
        if (isset($authData['success']) && $authData['success']) {
            $dealCount = isset($authData['deals']) ? count($authData['deals']) : 0;
            echo "   ✅ Authenticated deals found: $dealCount\n";
            
            if ($dealCount > 0) {
                echo "   📋 Sample authenticated deal data:\n";
                $firstDeal = $authData['deals'][0];
                echo "      - ID: " . ($firstDeal['id'] ?? 'N/A') . "\n";
                echo "      - Title: " . ($firstDeal['title'] ?? 'N/A') . "\n";
                echo "      - Status: " . ($firstDeal['status'] ?? 'N/A') . "\n";
                echo "      - Applies to: " . ($firstDeal['applies_to'] ?? 'N/A') . "\n";
            }
        }
        
    } else {
        echo "   ⚠️  Login failed: " . ($loginData['message'] ?? 'Unknown error') . "\n";
    }
    
} catch (RequestException $e) {
    echo "   ⚠️  Could not authenticate: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Compare with other deal endpoints
echo "3. Testing related deal endpoints for comparison:\n";

$endpoints = [
    '/deals' => 'All deals (authenticated)',
    '/categories-with-deals' => 'Categories with deals',
];

foreach ($endpoints as $endpoint => $description) {
    echo "   Testing $endpoint ($description):\n";
    
    try {
        $response = $client->get("$baseUrl$endpoint", [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
        
        $statusCode = $response->getStatusCode();
        echo "     ✅ Status: $statusCode\n";
        
    } catch (RequestException $e) {
        $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 'No response';
        echo "     ❌ Status: $statusCode\n";
        
        if ($statusCode == 401) {
            echo "     🔑 Requires authentication (expected)\n";
        }
    }
}

echo "\n=== TEST SUMMARY ===\n";
echo "The missing getActiveDeals() method has been added to DealController.\n";
echo "The /active-deals endpoint should now work properly.\n";
echo "\nIf you're still getting 'failed to load deals' in the Flutter app:\n";
echo "1. Make sure the Laravel server is running\n";
echo "2. Check that the user is authenticated in the Flutter app\n";
echo "3. Verify the API base URL in the Flutter app configuration\n";
echo "4. Check if there are any active deals in the database\n";
echo "\nNext steps:\n";
echo "- Test the Flutter app again\n";
echo "- Check the Laravel logs for any errors\n";
echo "- Verify that deals exist in the database\n";

?>