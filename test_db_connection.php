<?php

echo "Testing database connection...\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=marketplace_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected successfully\n";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "📊 Found {$result['count']} products in database\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM product_colors");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "🎨 Found {$result['count']} color variants in database\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
