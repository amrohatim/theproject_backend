<?php

// This script creates a simple text file as a placeholder

// Define paths
$publicPath = __DIR__ . '/public';
$imagesPath = $publicPath . '/images';
$placeholderPath = $imagesPath . '/placeholder.jpg';

echo "Creating simple placeholder file...\n";

// Ensure the images directory exists
if (!file_exists($imagesPath)) {
    echo "Creating images directory...\n";
    mkdir($imagesPath, 0755, true);
    echo "Images directory created.\n";
}

// Create a simple text file as a placeholder
$placeholderContent = "This is a placeholder for a missing image.";
file_put_contents($placeholderPath, $placeholderContent);

echo "Simple placeholder file created at: $placeholderPath\n";

// Also create a copy in the storage directory
$storagePath = __DIR__ . '/storage/app/public';
$storageProductsPath = $storagePath . '/products';

// Ensure the storage directories exist
if (!file_exists($storagePath)) {
    echo "Creating storage/app/public directory...\n";
    mkdir($storagePath, 0755, true);
    echo "Storage directory created.\n";
}

if (!file_exists($storageProductsPath)) {
    echo "Creating storage/app/public/products directory...\n";
    mkdir($storageProductsPath, 0755, true);
    echo "Products directory created.\n";
}

// Copy the placeholder to storage locations
copy($placeholderPath, $storagePath . '/placeholder.jpg');
copy($placeholderPath, $storageProductsPath . '/placeholder.jpg');

echo "Placeholder file copied to storage locations.\n";
echo "Done.\n";
