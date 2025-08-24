<?php

// Test script to verify API endpoints are working correctly
// This will help us debug the nearby branches issue

$baseUrl = 'http://127.0.0.1:8000/api';

// Test authentication token (you'll need to replace this with a valid token)
$authToken = 'Bearer YOUR_AUTH_TOKEN_HERE';

echo "=== API Endpoints Test ===\n";
echo "Base URL: $baseUrl\n\n";

// Function to make HTTP requests
function makeRequest($url, $headers = [], $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'response' => $response,
        'http_code' => $httpCode,
        'error' => $error
    ];
}

// Test 1: Check if user locations endpoint is accessible
echo "1. Testing /user/locations endpoint...\n";
$headers = [
    'Content-Type: application/json',
    'Accept: application/json',
    // 'Authorization: ' . $authToken  // Uncomment when you have a valid token
];

$result = makeRequest("$baseUrl/user/locations", $headers);
echo "   HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "   Error: " . $result['error'] . "\n";
}
if ($result['response']) {
    $data = json_decode($result['response'], true);
    if ($data) {
        echo "   Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "   Raw Response: " . $result['response'] . "\n";
    }
}
echo "\n";

// Test 2: Check if branches/nearby endpoint is accessible
echo "2. Testing /branches/nearby endpoint...\n";
$testLat = 25.2048; // Dubai coordinates
$testLng = 55.2708;
$testUrl = "$baseUrl/branches/nearby?latitude=$testLat&longitude=$testLng&radius=25&limit=10";

$result = makeRequest($testUrl, $headers);
echo "   HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "   Error: " . $result['error'] . "\n";
}
if ($result['response']) {
    $data = json_decode($result['response'], true);
    if ($data) {
        echo "   Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "   Raw Response: " . $result['response'] . "\n";
    }
}
echo "\n";

// Test 3: Check if branches endpoint (general) is accessible
echo "3. Testing /branches endpoint...\n";
$result = makeRequest("$baseUrl/branches", $headers);
echo "   HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "   Error: " . $result['error'] . "\n";
}
if ($result['response']) {
    $data = json_decode($result['response'], true);
    if ($data) {
        if (isset($data['branches'])) {
            echo "   Found " . count($data['branches']) . " branches\n";
            echo "   First few branches:\n";
            foreach (array_slice($data['branches'], 0, 3) as $branch) {
                echo "     - " . $branch['name'] . " (ID: " . $branch['id'] . ")\n";
            }
        } else {
            echo "   Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "   Raw Response: " . $result['response'] . "\n";
    }
}
echo "\n";

echo "=== Test Complete ===\n";
echo "Note: If you get 401 errors, you need to provide a valid authentication token.\n";
echo "To get a token, login through the app or use the /login endpoint.\n";

?>
