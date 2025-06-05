<?php

// Check if directories exist
echo "Checking directories...\n";
echo "public/images exists: " . (is_dir('public/images') ? 'Yes' : 'No') . "\n";
echo "public/images/products exists: " . (is_dir('public/images/products') ? 'Yes' : 'No') . "\n";
echo "public/storage exists: " . (is_dir('public/storage') ? 'Yes' : 'No') . "\n";
echo "storage/app/public exists: " . (is_dir('storage/app/public') ? 'Yes' : 'No') . "\n";
echo "storage/app/public/products exists: " . (is_dir('storage/app/public/products') ? 'Yes' : 'No') . "\n";

// Create directories if they don't exist
if (!is_dir('public/images')) {
    echo "Creating public/images directory...\n";
    mkdir('public/images', 0755, true);
}

if (!is_dir('public/images/products')) {
    echo "Creating public/images/products directory...\n";
    mkdir('public/images/products', 0755, true);
}

if (!is_dir('public/images/services')) {
    echo "Creating public/images/services directory...\n";
    mkdir('public/images/services', 0755, true);
}

// Copy a test image to public/images/products
echo "\nCopying test image...\n";
if (is_dir('storage/app/public/products')) {
    $files = scandir('storage/app/public/products');
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && is_file('storage/app/public/products/' . $file)) {
            $source = 'storage/app/public/products/' . $file;
            $destination = 'public/images/products/' . $file;
            if (copy($source, $destination)) {
                echo "Copied test image: " . $file . " to " . $destination . "\n";
                break; // Just copy one file as a test
            } else {
                echo "Failed to copy test image: " . $file . "\n";
            }
        }
    }
} else {
    echo "Source product directory does not exist\n";
}

// Create a test image if none exists
if (!is_dir('public/images/products') || count(scandir('public/images/products')) <= 2) {
    echo "Creating a test image...\n";
    $testImage = 'public/images/products/test-image.jpg';
    $imageData = file_get_contents('https://via.placeholder.com/150');
    if ($imageData !== false) {
        file_put_contents($testImage, $imageData);
        echo "Created test image: " . $testImage . "\n";
    } else {
        echo "Failed to create test image\n";
    }
}

echo "\nCheck complete\n";
