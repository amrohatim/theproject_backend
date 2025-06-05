<?php

// Create test image directories
if (!is_dir(__DIR__ . '/images')) {
    mkdir(__DIR__ . '/images', 0755, true);
    echo "Created images directory<br>";
}

if (!is_dir(__DIR__ . '/images/products')) {
    mkdir(__DIR__ . '/images/products', 0755, true);
    echo "Created images/products directory<br>";
}

// Create a test image
$testImage = __DIR__ . '/images/products/test-product.jpg';
$imageData = file_get_contents('https://via.placeholder.com/150');
if ($imageData !== false) {
    file_put_contents($testImage, $imageData);
    echo "Created test image: " . $testImage . "<br>";
} else {
    echo "Failed to create test image<br>";
}

// Display the test image
echo "<h2>Test Image</h2>";
echo '<img src="/images/products/test-product.jpg" alt="Test Product">';

// Update the database to use this test image for all products
try {
    // Connect to the database
    $db = new PDO('mysql:host=localhost;dbname=marketplace', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update all products to use the test image
    $stmt = $db->prepare("UPDATE products SET image = 'images/products/test-product.jpg'");
    $stmt->execute();
    
    echo "<p>Updated all products to use the test image</p>";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

echo "<p>Done!</p>";
