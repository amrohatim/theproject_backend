<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Create a new Capsule instance
$capsule = new Capsule;

// Add connection
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);

// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// Boot Eloquent
$capsule->bootEloquent();

echo "🔍 Testing Featured Products Database Query\n";
echo "==========================================\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $connection = $capsule->getConnection();
    $connection->getPdo();
    echo "✅ Database connection successful\n\n";

    // Check if products table exists
    echo "2. Checking if products table exists...\n";
    $tables = $connection->select("SELECT name FROM sqlite_master WHERE type='table' AND name='products'");
    if (empty($tables)) {
        echo "❌ Products table does not exist\n";
        exit(1);
    }
    echo "✅ Products table exists\n\n";

    // Check total products count
    echo "3. Checking total products count...\n";
    $totalProducts = $connection->table('products')->count();
    echo "📊 Total products in database: $totalProducts\n\n";

    if ($totalProducts == 0) {
        echo "❌ No products found in database. Please run the seeders first.\n";
        echo "Run: php artisan db:seed\n";
        exit(1);
    }

    // Check featured products count
    echo "4. Checking featured products count...\n";
    $featuredProducts = $connection->table('products')->where('featured', true)->count();
    echo "📊 Featured products in database: $featuredProducts\n\n";

    if ($featuredProducts == 0) {
        echo "❌ No featured products found in database.\n";
        echo "🔧 Let's mark some products as featured...\n\n";
        
        // Mark first 5 products as featured
        $updated = $connection->table('products')
            ->limit(5)
            ->update(['featured' => true]);
        
        echo "✅ Marked $updated products as featured\n\n";
        
        // Re-check featured products count
        $featuredProducts = $connection->table('products')->where('featured', true)->count();
        echo "📊 Featured products after update: $featuredProducts\n\n";
    }

    // Show featured products details
    echo "5. Featured products details:\n";
    $featured = $connection->table('products')
        ->where('featured', true)
        ->select('id', 'name', 'price', 'featured', 'is_available')
        ->get();

    foreach ($featured as $product) {
        echo "   - ID: {$product->id}, Name: {$product->name}, Price: \${$product->price}, Available: " . ($product->is_available ? 'Yes' : 'No') . "\n";
    }
    echo "\n";

    // Test the API endpoint simulation
    echo "6. Testing API endpoint simulation...\n";
    $apiProducts = $connection->table('products')
        ->where('featured', true)
        ->where('is_available', true)
        ->with(['branch', 'category'])
        ->get();

    echo "📊 API would return: " . count($apiProducts) . " featured products\n\n";

    echo "✅ All tests completed successfully!\n";
    echo "🎉 Featured products are available in the database.\n\n";

    echo "Next steps:\n";
    echo "1. Make sure Laravel server is running: php artisan serve --host=0.0.0.0 --port=8000\n";
    echo "2. Check Flutter app API configuration\n";
    echo "3. Check network connectivity between Flutter app and Laravel server\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
