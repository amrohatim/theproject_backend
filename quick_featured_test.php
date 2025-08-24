<?php

// Quick test script to verify featured products functionality
echo "🚀 Quick Featured Products Test\n";
echo "==============================\n\n";

// Test 1: Check if we can connect to the database
echo "1. Testing database connection...\n";
try {
    // Try MySQL first
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketplace_windsurf', 'root', '');
    echo "✅ MySQL connection successful\n";
    $dbType = 'mysql';
} catch (PDOException $e) {
    // Try SQLite as fallback
    try {
        $sqliteFile = __DIR__ . '/database/database.sqlite';
        if (file_exists($sqliteFile)) {
            $pdo = new PDO('sqlite:' . $sqliteFile);
            echo "✅ SQLite connection successful\n";
            $dbType = 'sqlite';
        } else {
            echo "❌ No database found\n";
            exit(1);
        }
    } catch (PDOException $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Test 2: Check products table
echo "\n2. Checking products table...\n";
try {
    if ($dbType === 'mysql') {
        $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    } else {
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='products'");
    }
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Products table exists\n";
        
        // Count total products
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 Total products: " . $result['count'] . "\n";
        
        if ($result['count'] == 0) {
            echo "❌ No products in database. Please run: php artisan db:seed\n";
            exit(1);
        }
        
        // Count featured products
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE featured = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 Featured products: " . $result['count'] . "\n";
        
        if ($result['count'] == 0) {
            echo "🔧 Creating featured products...\n";
            $stmt = $pdo->prepare("UPDATE products SET featured = 1 WHERE id IN (SELECT id FROM (SELECT id FROM products WHERE is_available = 1 LIMIT 5) as temp)");
            $stmt->execute();
            echo "✅ Created featured products\n";
        }
        
    } else {
        echo "❌ Products table does not exist\n";
        exit(1);
    }
} catch (PDOException $e) {
    echo "❌ Error checking products table: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Simulate API response
echo "\n3. Simulating API response...\n";
try {
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.price, p.featured, p.is_available, 
               b.name as branch_name, c.name as category_name
        FROM products p 
        LEFT JOIN branches b ON p.branch_id = b.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.featured = 1 AND p.is_available = 1 
        LIMIT 10
    ");
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($products) > 0) {
        echo "✅ Found " . count($products) . " featured products\n";
        
        $apiResponse = [
            'success' => true,
            'products' => $products
        ];
        
        echo "\n📋 API Response Preview:\n";
        echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n";
        
    } else {
        echo "❌ No featured products found\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error simulating API response: " . $e->getMessage() . "\n";
}

// Test 4: Check if Laravel server is accessible
echo "\n4. Testing Laravel server accessibility...\n";
$serverUrl = 'http://192.168.70.48:8000';

$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'method' => 'GET',
        'header' => 'Accept: application/json'
    ]
]);

$result = @file_get_contents($serverUrl, false, $context);
if ($result !== false) {
    echo "✅ Laravel server is accessible at $serverUrl\n";
} else {
    echo "❌ Laravel server is not accessible at $serverUrl\n";
    echo "   Please start the server with: php artisan serve --host=0.0.0.0 --port=8000\n";
}

echo "\n✅ Quick test completed!\n";
echo "\nNext steps:\n";
echo "1. Start Laravel server: php artisan serve --host=0.0.0.0 --port=8000\n";
echo "2. Run Flutter app and check debug console\n";
echo "3. Use the refresh button in the app to test API calls\n";
