<?php

echo "🔍 Simple Laravel Test\n";
echo "=====================\n\n";

// Test 1: Check if Laravel can be loaded
echo "1. Testing Laravel bootstrap...\n";
try {
    require_once 'vendor/autoload.php';
    echo "✅ Autoloader loaded\n";
    
    $app = require_once 'bootstrap/app.php';
    echo "✅ Laravel app loaded\n";
    
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    echo "✅ Laravel bootstrapped\n\n";
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check database connection
echo "2. Testing database connection...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketplace_windsurf', 'root', '');
    echo "✅ MySQL connection successful\n";
    
    // Test if products table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Products table exists\n";
        
        // Count products
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 Total products: " . $result['count'] . "\n";
        
        // Count featured products
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE featured = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 Featured products: " . $result['count'] . "\n";
        
        if ($result['count'] == 0) {
            echo "🔧 No featured products found. Creating some...\n";
            $stmt = $pdo->prepare("UPDATE products SET featured = 1 LIMIT 5");
            $stmt->execute();
            echo "✅ Marked 5 products as featured\n";
        }
        
    } else {
        echo "❌ Products table does not exist\n";
    }
    
} catch (PDOException $e) {
    echo "❌ MySQL connection failed: " . $e->getMessage() . "\n";
    
    // Try SQLite as fallback
    echo "\n3. Trying SQLite as fallback...\n";
    try {
        $sqliteFile = __DIR__ . '/database/database.sqlite';
        if (file_exists($sqliteFile)) {
            $pdo = new PDO('sqlite:' . $sqliteFile);
            echo "✅ SQLite connection successful\n";
            
            // Test if products table exists
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='products'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Products table exists in SQLite\n";
                
                // Count products
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "📊 Total products: " . $result['count'] . "\n";
                
                // Count featured products
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE featured = 1");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "📊 Featured products: " . $result['count'] . "\n";
                
                if ($result['count'] == 0) {
                    echo "🔧 No featured products found. Creating some...\n";
                    $stmt = $pdo->prepare("UPDATE products SET featured = 1 WHERE id IN (SELECT id FROM products LIMIT 5)");
                    $stmt->execute();
                    echo "✅ Marked 5 products as featured\n";
                }
                
            } else {
                echo "❌ Products table does not exist in SQLite\n";
            }
        } else {
            echo "❌ SQLite database file not found\n";
        }
    } catch (PDOException $e) {
        echo "❌ SQLite connection failed: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Test completed!\n";
