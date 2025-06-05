<?php

// Simple debug script to check image paths

// Check if directories exist
echo "<h1>Directory Check</h1>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>images/ exists: " . (is_dir(__DIR__ . '/images') ? 'Yes' : 'No') . "</p>";
echo "<p>images/products/ exists: " . (is_dir(__DIR__ . '/images/products') ? 'Yes' : 'No') . "</p>";
echo "<p>storage/ exists: " . (is_dir(__DIR__ . '/storage') ? 'Yes' : 'No') . "</p>";

// Create directories if they don't exist
if (!is_dir(__DIR__ . '/images')) {
    echo "<p>Creating images/ directory...</p>";
    mkdir(__DIR__ . '/images', 0755, true);
    echo "<p>Directory created</p>";
}

if (!is_dir(__DIR__ . '/images/products')) {
    echo "<p>Creating images/products/ directory...</p>";
    mkdir(__DIR__ . '/images/products', 0755, true);
    echo "<p>Directory created</p>";
}

if (!is_dir(__DIR__ . '/images/services')) {
    echo "<p>Creating images/services/ directory...</p>";
    mkdir(__DIR__ . '/images/services', 0755, true);
    echo "<p>Directory created</p>";
}

// Create a test image
echo "<h1>Test Image</h1>";
$testImage = __DIR__ . '/images/products/test-image.jpg';
if (!file_exists($testImage)) {
    echo "<p>Creating a test image...</p>";
    $imageData = file_get_contents('https://via.placeholder.com/150');
    if ($imageData !== false) {
        file_put_contents($testImage, $imageData);
        echo "<p>Created test image: " . $testImage . "</p>";
    } else {
        echo "<p>Failed to create test image</p>";
    }
} else {
    echo "<p>Test image already exists: " . $testImage . "</p>";
}

// Display the test image
echo "<h1>Image Display Test</h1>";
echo "<p>Direct image tag:</p>";
echo '<img src="/images/products/test-image.jpg" alt="Test Image">';

// Check if we can access the image
echo "<h1>Image Access Check</h1>";
$imageUrl = '/images/products/test-image.jpg';
$fullUrl = 'http://' . $_SERVER['HTTP_HOST'] . $imageUrl;
echo "<p>Image URL: " . $fullUrl . "</p>";

$headers = get_headers($fullUrl);
echo "<p>HTTP Response: " . $headers[0] . "</p>";

// List all files in the images/products directory
echo "<h1>Files in images/products/</h1>";
if (is_dir(__DIR__ . '/images/products')) {
    $files = scandir(__DIR__ . '/images/products');
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>" . $file . " - <img src='/images/products/" . $file . "' alt='" . $file . "' style='max-width: 100px;'></li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>Directory does not exist</p>";
}

echo "<h1>Debug Complete</h1>";
