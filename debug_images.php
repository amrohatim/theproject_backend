<?php

// This script helps debug image paths and display issues

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the storage path
$storagePath = storage_path('app/public');
$publicPath = public_path();

echo "Storage path: " . $storagePath . "\n";
echo "Public path: " . $publicPath . "\n";

// Check if the storage link exists
$storageLink = file_exists(public_path('storage'));
echo "Storage link exists: " . ($storageLink ? 'Yes' : 'No') . "\n";

// Get all product images from the database
$products = \App\Models\Product::all();
echo "\nProduct images in database:\n";
echo "-------------------------\n";
foreach ($products as $product) {
    echo "Product ID: " . $product->id . "\n";
    echo "Image path: " . $product->image . "\n";
    
    // Check if the image exists in storage
    $imagePath = str_replace('/storage/', '', $product->image);
    $storageExists = file_exists(storage_path('app/public/' . $imagePath));
    echo "Exists in storage: " . ($storageExists ? 'Yes' : 'No') . "\n";
    
    // Check if the image exists in public/images/products
    $publicImagePath = 'images/products/' . basename($product->image ?? '');
    $publicExists = file_exists(public_path($publicImagePath));
    echo "Exists in public/images/products: " . ($publicExists ? 'Yes' : 'No') . "\n";
    
    // Get the raw image attribute
    $rawImage = $product->getRawAttribute('image');
    echo "Raw image attribute: " . $rawImage . "\n";
    
    echo "-------------------------\n";
}

// List all files in the storage/app/public/products directory
echo "\nFiles in storage/app/public/products:\n";
if (is_dir(storage_path('app/public/products'))) {
    $files = scandir(storage_path('app/public/products'));
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo $file . "\n";
        }
    }
} else {
    echo "Directory does not exist\n";
}

// List all files in the public/images/products directory
echo "\nFiles in public/images/products:\n";
if (is_dir(public_path('images/products'))) {
    $files = scandir(public_path('images/products'));
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo $file . "\n";
        }
    }
} else {
    echo "Directory does not exist\n";
}

// Create the public/images/products directory if it doesn't exist
if (!is_dir(public_path('images/products'))) {
    echo "\nCreating public/images/products directory...\n";
    mkdir(public_path('images/products'), 0755, true);
    echo "Directory created\n";
}

// Copy all images from storage to public/images/products
echo "\nCopying images from storage to public/images/products...\n";
if (is_dir(storage_path('app/public/products'))) {
    $files = scandir(storage_path('app/public/products'));
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $source = storage_path('app/public/products/' . $file);
            $destination = public_path('images/products/' . $file);
            if (copy($source, $destination)) {
                echo "Copied: " . $file . "\n";
            } else {
                echo "Failed to copy: " . $file . "\n";
            }
        }
    }
} else {
    echo "Source directory does not exist\n";
}

echo "\nDebug complete\n";
