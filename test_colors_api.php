<?php

// This script tests the /products/{productId}/colors API endpoint

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductColor;

echo "Testing the /products/{productId}/colors API endpoint\n\n";

// 1. Find a product with colors
$products = Product::has('colors')->with('colors')->get();

if ($products->isEmpty()) {
    echo "No products with colors found. Creating a test product with colors...\n";

    // Find any product
    $product = Product::first();

    if (!$product) {
        echo "No products found in the database. Please create a product first.\n";
        exit(1);
    }

    // Create some test colors for the product
    $colors = [
        [
            'name' => 'Red',
            'color_code' => '#FF0000',
            'price_adjustment' => 0,
            'stock' => 10,
            'display_order' => 0,
            'is_default' => true,
        ],
        [
            'name' => 'Blue',
            'color_code' => '#0000FF',
            'price_adjustment' => 5,
            'stock' => 5,
            'display_order' => 1,
            'is_default' => false,
        ],
        [
            'name' => 'Green',
            'color_code' => '#00FF00',
            'price_adjustment' => 3,
            'stock' => 8,
            'display_order' => 2,
            'is_default' => false,
        ],
    ];

    foreach ($colors as $colorData) {
        $product->colors()->create($colorData);
    }

    echo "Created test colors for product ID: {$product->id}\n";
    $productId = $product->id;
} else {
    $product = $products->first();
    $productId = $product->id;
    echo "Found product with colors. Product ID: {$productId}\n";
}

// 2. Create a test user and authenticate
$user = User::first();

if (!$user) {
    echo "No users found in the database. Please create a user first.\n";
    exit(1);
}

// Login the user
Auth::login($user);
$token = $user->createToken('test-token')->plainTextToken;

echo "Authenticated as user: {$user->name} (ID: {$user->id})\n";
echo "Token: {$token}\n\n";

// 3. Make a request to the API endpoint
$url = "http://localhost:8000/api/products/{$productId}/colors";
echo "Making request to: {$url}\n";

// Create a cURL request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: {$httpCode}\n\n";

// 4. Display the response
if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "Response:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";

    if (isset($data['colors']) && is_array($data['colors'])) {
        echo "\nFound " . count($data['colors']) . " colors for product ID {$productId}:\n";
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
    echo "Error Response:\n{$response}\n";
}

echo "\nTest completed.\n";
