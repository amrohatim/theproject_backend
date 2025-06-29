<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🔍 Diagnosing 500 Internal Server Error\n";
echo "=======================================\n\n";

try {
    // 1. Test basic database connection
    echo "1. Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";

    // 2. Check if products table exists and has featured column
    echo "2. Checking products table structure...\n";
    $columns = DB::select("PRAGMA table_info(products)");
    
    $hasFeaturedColumn = false;
    $hasIsAvailableColumn = false;
    
    foreach ($columns as $column) {
        if ($column->name === 'featured') {
            $hasFeaturedColumn = true;
        }
        if ($column->name === 'is_available') {
            $hasIsAvailableColumn = true;
        }
    }
    
    if (!$hasFeaturedColumn) {
        echo "❌ Featured column missing from products table\n";
        echo "🔧 Run: php artisan migrate\n";
        exit(1);
    }
    
    if (!$hasIsAvailableColumn) {
        echo "❌ is_available column missing from products table\n";
        echo "🔧 Run: php artisan migrate\n";
        exit(1);
    }
    
    echo "✅ Required columns exist (featured, is_available)\n\n";

    // 3. Check current data
    echo "3. Checking existing data...\n";
    $totalProducts = Product::count();
    echo "📊 Total products: $totalProducts\n";
    
    if ($totalProducts > 0) {
        $featuredProducts = Product::where('featured', true)->count();
        $availableProducts = Product::where('is_available', true)->count();
        
        echo "📊 Featured products: $featuredProducts\n";
        echo "📊 Available products: $availableProducts\n";
        
        // Show sample products
        $sampleProducts = Product::limit(3)->get(['id', 'name', 'featured', 'is_available']);
        echo "📋 Sample products:\n";
        foreach ($sampleProducts as $product) {
            $featured = $product->featured ? 'Yes' : 'No';
            $available = $product->is_available ? 'Yes' : 'No';
            echo "  - ID: {$product->id}, Name: {$product->name}, Featured: $featured, Available: $available\n";
        }
    } else {
        echo "📊 No products found in database\n";
    }
    echo "\n";

    // 4. Test the exact query that causes the 500 error
    echo "4. Testing the problematic query...\n";
    
    try {
        // This is the exact query from ProductController@index with featured=true
        $query = Product::with([
            'branch',
            'category', 
            'colors',
            'sizes',
            'colorSizes.color',
            'colorSizes.size'
        ]);
        
        echo "📊 Base query created successfully\n";
        
        // Apply featured filter
        $query->where('featured', true);
        echo "📊 Featured filter applied\n";
        
        // Get SQL for debugging
        echo "📊 SQL: " . $query->toSql() . "\n";
        echo "📊 Bindings: " . json_encode($query->getBindings()) . "\n";
        
        // Try to execute the query
        $products = $query->paginate(10);
        echo "✅ Query executed successfully\n";
        echo "📊 Results: " . $products->count() . " products\n";
        
    } catch (Exception $e) {
        echo "❌ Query failed: " . $e->getMessage() . "\n";
        echo "📋 This is likely the cause of the 500 error!\n\n";
        
        // Try simpler queries to isolate the issue
        echo "🔍 Testing simpler queries to isolate the issue...\n";
        
        try {
            $simpleCount = Product::count();
            echo "✅ Simple count query works: $simpleCount\n";
        } catch (Exception $e) {
            echo "❌ Simple count failed: " . $e->getMessage() . "\n";
        }
        
        try {
            $featuredCount = Product::where('featured', true)->count();
            echo "✅ Featured filter works: $featuredCount\n";
        } catch (Exception $e) {
            echo "❌ Featured filter failed: " . $e->getMessage() . "\n";
        }
        
        // Test each relationship individually
        $relationships = ['branch', 'category', 'colors', 'sizes', 'colorSizes.color', 'colorSizes.size'];
        
        foreach ($relationships as $relationship) {
            try {
                $testQuery = Product::with([$relationship])->where('featured', true)->limit(1)->get();
                echo "✅ Relationship '$relationship' works\n";
            } catch (Exception $e) {
                echo "❌ Relationship '$relationship' failed: " . $e->getMessage() . "\n";
            }
        }
    }

    // 5. Check if related tables exist
    echo "\n5. Checking related tables...\n";
    $requiredTables = ['branches', 'categories', 'product_colors', 'product_sizes', 'product_color_sizes'];
    
    foreach ($requiredTables as $table) {
        try {
            $exists = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
            if (empty($exists)) {
                echo "❌ Table '$table' does not exist\n";
            } else {
                echo "✅ Table '$table' exists\n";
            }
        } catch (Exception $e) {
            echo "❌ Error checking table '$table': " . $e->getMessage() . "\n";
        }
    }

    echo "\n6. Testing API endpoint directly...\n";
    
    // Test the actual API endpoint
    $baseUrl = 'http://127.0.0.1:8000'; // Local test
    $endpoint = '/api/products?featured=true';
    
    echo "📡 Testing: $baseUrl$endpoint\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ Connection error: $error\n";
        echo "🔧 Laravel server might not be running\n";
        echo "🔧 Start with: php artisan serve\n";
    } else {
        echo "📊 HTTP Status: $httpCode\n";
        
        if ($httpCode === 200) {
            echo "✅ API endpoint works!\n";
            $data = json_decode($response, true);
            if ($data && isset($data['products'])) {
                echo "📊 API returned products successfully\n";
            }
        } elseif ($httpCode === 500) {
            echo "❌ 500 error confirmed\n";
            echo "📋 Response: " . substr($response, 0, 200) . "...\n";
        }
    }

    echo "\n🎯 Summary and Next Steps:\n";
    echo "========================\n";
    
    if ($totalProducts == 0) {
        echo "⚠️  Database is empty - this might be the root cause\n";
        echo "🔧 Consider adding some test products manually\n";
    } else {
        echo "✅ Database has products\n";
    }

} catch (Exception $e) {
    echo "❌ Critical error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
