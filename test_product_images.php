<?php

// This script tests the product image handling logic

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Facades\Log;

// Enable query logging
\Illuminate\Support\Facades\DB::enableQueryLog();

echo "Testing product image handling...\n\n";

// Get a product with colors
$product = Product::with('colors')->first();

if (!$product) {
    echo "No products found in the database.\n";
    exit;
}

echo "Product ID: {$product->id}\n";
echo "Product Name: {$product->name}\n";
echo "Raw Image Path: " . ($product->getRawOriginal('image') ?? 'null') . "\n";
echo "Processed Image URL: {$product->image}\n\n";

// Check if the product has colors
$colors = $product->colors;
echo "Product has " . $colors->count() . " colors.\n";

if ($colors->count() > 0) {
    echo "Colors:\n";
    foreach ($colors as $color) {
        echo "- {$color->name} (Default: " . ($color->is_default ? 'Yes' : 'No') . ")\n";
        echo "  Raw Image Path: " . ($color->getRawOriginal('image') ?? 'null') . "\n";
        echo "  Processed Image URL: {$color->image}\n";
    }
    echo "\n";
}

// Get the default color
$defaultColor = $product->colors()->where('is_default', true)->first();
echo "Default Color: " . ($defaultColor ? $defaultColor->name : 'None') . "\n";
if ($defaultColor) {
    echo "Default Color Image: {$defaultColor->image}\n\n";
}

// Test the getDefaultColorImage method
echo "Default Color Image from method: {$product->getDefaultColorImage()}\n\n";

// Test updating the product's main image from a color image
if ($defaultColor && $defaultColor->image) {
    echo "Updating product's main image from default color image...\n";
    $product->updateMainImageFromColorImage($defaultColor->getRawOriginal('image'));
    $product->refresh();
    echo "New Raw Image Path: " . ($product->getRawOriginal('image') ?? 'null') . "\n";
    echo "New Processed Image URL: {$product->image}\n\n";
}

// Test creating a new color and setting it as default
echo "Creating a new color and setting it as default...\n";
// First, remove default from all existing colors
$product->colors()->update(['is_default' => false]);

// Create a new color
$newColor = $product->colors()->create([
    'name' => 'Test Color',
    'color_code' => '#FF5500',
    'image' => 'storage/product-colors/test-color.jpg',
    'price_adjustment' => 0,
    'stock' => 10,
    'display_order' => 999,
    'is_default' => true,
]);

echo "New Color ID: {$newColor->id}\n";
echo "New Color Name: {$newColor->name}\n";
echo "New Color Image: {$newColor->image}\n\n";

// Refresh the product
$product->refresh();

// Test if the product's main image is updated
echo "Product's main image after adding new default color:\n";
echo "Raw Image Path: " . ($product->getRawOriginal('image') ?? 'null') . "\n";
echo "Processed Image URL: {$product->image}\n\n";

// Clean up - delete the test color
$newColor->delete();
echo "Test color deleted.\n";

// Show the queries that were executed
$queries = \Illuminate\Support\Facades\DB::getQueryLog();
echo "\nQueries executed: " . count($queries) . "\n";
foreach ($queries as $query) {
    echo "- " . $query['query'] . "\n";
}

echo "\nTest completed.\n";
