<?php

// This script tests the /products/{productId}/colors API endpoint from the command line

// Check if the required parameters are provided
$productId = $argv[1] ?? 38; // Default to product ID 38
$token = $argv[2] ?? null;

echo "Testing API endpoint: /products/{$productId}/colors\n";

if (empty($token)) {
    echo "No token provided. Attempting to generate one...\n";

    // Include the autoloader
    require __DIR__ . '/vendor/autoload.php';

    // Initialize Laravel application
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Find a user to authenticate
    $user = \App\Models\User::first();

    if (!$user) {
        echo "No users found in the database. Please create a user first.\n";
        exit(1);
    }

    // Create a token
    $token = $user->createToken('api-test-token')->plainTextToken;

    echo "Generated token for user: {$user->name} (ID: {$user->id})\n";
    echo "Token: {$token}\n\n";
}

// Set up the cURL request
$url = "http://localhost:8000/api/products/{$productId}/colors";
echo "Making request to: {$url}\n";
echo "Using token: {$token}\n\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FAILONERROR, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    "Authorization: Bearer {$token}",
]);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Display the results
echo "HTTP Status Code: {$httpCode}\n\n";

if ($response === false) {
    echo "Error: {$error}\n";
} else {
    // Pretty print the JSON response
    $data = json_decode($response, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Response:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

        if (isset($data['colors']) && is_array($data['colors'])) {
            echo "Found " . count($data['colors']) . " colors for product ID {$productId}:\n";
            foreach ($data['colors'] as $index => $color) {
                echo ($index + 1) . ". {$color['name']} ({$color['color_code']})\n";
                echo "   Price Adjustment: \${$color['price_adjustment']}, Stock: {$color['stock']}\n";
                echo "   Default: " . ($color['is_default'] ? 'Yes' : 'No') . "\n";
                if (!empty($color['image'])) {
                    echo "   Image: {$color['image']}\n";
                }
                echo "\n";
            }
        } else {
            echo "No colors found in the response.\n";
        }
    } else {
        echo "Raw Response (not valid JSON):\n{$response}\n";
    }
}

echo "Test completed.\n";
