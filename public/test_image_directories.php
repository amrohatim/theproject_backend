<?php
// Test script to check if image directories are writable

echo "<h1>Testing Image Directories</h1>";

// Test products directory
$productsDir = __DIR__ . '/images/products';
echo "<h2>Products Directory</h2>";
echo "Path: " . $productsDir . "<br>";
echo "Exists: " . (file_exists($productsDir) ? 'Yes' : 'No') . "<br>";
echo "Is Directory: " . (is_dir($productsDir) ? 'Yes' : 'No') . "<br>";
echo "Is Writable: " . (is_writable($productsDir) ? 'Yes' : 'No') . "<br>";

// Test services directory
$servicesDir = __DIR__ . '/images/services';
echo "<h2>Services Directory</h2>";
echo "Path: " . $servicesDir . "<br>";
echo "Exists: " . (file_exists($servicesDir) ? 'Yes' : 'No') . "<br>";
echo "Is Directory: " . (is_dir($servicesDir) ? 'Yes' : 'No') . "<br>";
echo "Is Writable: " . (is_writable($servicesDir) ? 'Yes' : 'No') . "<br>";

// Try to create a test file in each directory
echo "<h2>Testing Write Permissions</h2>";

// Test writing to products directory
$testProductFile = $productsDir . '/test.txt';
$productWriteResult = file_put_contents($testProductFile, 'Test content');
echo "Write to products directory: " . ($productWriteResult !== false ? 'Success' : 'Failed') . "<br>";
if ($productWriteResult !== false) {
    unlink($testProductFile);
    echo "Test file in products directory removed.<br>";
}

// Test writing to services directory
$testServiceFile = $servicesDir . '/test.txt';
$serviceWriteResult = file_put_contents($testServiceFile, 'Test content');
echo "Write to services directory: " . ($serviceWriteResult !== false ? 'Success' : 'Failed') . "<br>";
if ($serviceWriteResult !== false) {
    unlink($testServiceFile);
    echo "Test file in services directory removed.<br>";
}

// Check storage directory
$storageDir = __DIR__ . '/storage';
echo "<h2>Storage Directory</h2>";
echo "Path: " . $storageDir . "<br>";
echo "Exists: " . (file_exists($storageDir) ? 'Yes' : 'No') . "<br>";
echo "Is Directory: " . (is_dir($storageDir) ? 'Yes' : 'No') . "<br>";
echo "Is Writable: " . (is_writable($storageDir) ? 'Yes' : 'No') . "<br>";

// Check storage/app/public directory
$storagePublicDir = __DIR__ . '/storage/app/public';
echo "<h2>Storage Public Directory</h2>";
echo "Path: " . $storagePublicDir . "<br>";
echo "Exists: " . (file_exists($storagePublicDir) ? 'Yes' : 'No') . "<br>";
echo "Is Directory: " . (is_dir($storagePublicDir) ? 'Yes' : 'No') . "<br>";
echo "Is Writable: " . (is_writable($storagePublicDir) ? 'Yes' : 'No') . "<br>";

// Display current image paths from database
echo "<h2>Current Image Paths in Database</h2>";
try {
    // Connect to database
    $dbConfig = include(__DIR__ . '/../config/database.php');
    $connection = $dbConfig['connections'][$dbConfig['default']];
    
    $pdo = new PDO(
        "mysql:host={$connection['host']};dbname={$connection['database']}",
        $connection['username'],
        $connection['password']
    );
    
    // Get product images
    $stmt = $pdo->query("SELECT id, name, image FROM products");
    echo "<h3>Products</h3>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Image Path</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['image']}</td></tr>";
    }
    echo "</table>";
    
    // Get service images
    $stmt = $pdo->query("SELECT id, name, image FROM services");
    echo "<h3>Services</h3>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Image Path</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['image']}</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
}
