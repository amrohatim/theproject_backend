<?php

$db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketplace_windsurf;charset=utf8mb4', 'root', '');

echo "Checking database...\n";

try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Products count: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        $stmt = $db->query("SELECT id, name FROM products LIMIT 5");
        echo "\nSample products:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  ID: {$row['id']}, Name: {$row['name']}\n";
        }
    }
    
    // Check if product_colors table exists
    $stmt = $db->query("SHOW TABLES LIKE 'product_colors'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        $stmt = $db->query("SELECT COUNT(*) as count FROM product_colors");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nProduct colors count: " . $result['count'] . "\n";
    } else {
        echo "\nProduct colors table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
