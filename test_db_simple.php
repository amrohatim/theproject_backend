<?php

// Simple database test without Laravel
try {
    // Connect to SQLite database
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful\n";
    
    // Check if products table exists
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='products'");
    $table = $stmt->fetch();
    
    if (!$table) {
        echo "❌ Products table does not exist\n";
        exit(1);
    }
    
    echo "✅ Products table exists\n";
    
    // Check table structure
    echo "\n📋 Products table structure:\n";
    $stmt = $pdo->query("PRAGMA table_info(products)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasFeaturedColumn = false;
    foreach ($columns as $column) {
        echo "  - {$column['name']} ({$column['type']})\n";
        if ($column['name'] === 'featured') {
            $hasFeaturedColumn = true;
        }
    }
    
    if (!$hasFeaturedColumn) {
        echo "❌ Featured column does not exist in products table\n";
        exit(1);
    }
    
    echo "✅ Featured column exists\n";
    
    // Count total products
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalProducts = $result['count'];
    
    echo "\n📊 Total products: $totalProducts\n";
    
    if ($totalProducts == 0) {
        echo "❌ No products found. Database needs to be seeded.\n";
        exit(1);
    }
    
    // Count featured products
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE featured = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $featuredProducts = $result['count'];
    
    echo "📊 Featured products: $featuredProducts\n";
    
    if ($featuredProducts == 0) {
        echo "\n🔧 No featured products found. Let's create some...\n";
        
        // Mark first 5 products as featured
        $stmt = $pdo->prepare("UPDATE products SET featured = 1 WHERE id IN (SELECT id FROM products LIMIT 5)");
        $stmt->execute();
        
        echo "✅ Marked first 5 products as featured\n";
        
        // Re-count featured products
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE featured = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $featuredProducts = $result['count'];
        
        echo "📊 Featured products after update: $featuredProducts\n";
    }
    
    // Show featured products
    echo "\n📋 Featured products list:\n";
    $stmt = $pdo->query("SELECT id, name, price, featured, is_available FROM products WHERE featured = 1 LIMIT 10");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        $available = $product['is_available'] ? 'Yes' : 'No';
        echo "  - ID: {$product['id']}, Name: {$product['name']}, Price: \${$product['price']}, Available: $available\n";
    }
    
    // Test the query that the API would use
    echo "\n🔍 Testing API query (featured=true AND is_available=true):\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE featured = 1 AND is_available = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $apiProducts = $result['count'];
    
    echo "📊 Products API would return: $apiProducts\n";
    
    if ($apiProducts > 0) {
        echo "✅ API query should work correctly!\n";
    } else {
        echo "❌ API query would return no results. Check is_available values.\n";
    }
    
    echo "\n🎉 Database test completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
