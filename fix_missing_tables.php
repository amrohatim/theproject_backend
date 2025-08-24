<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "🔧 Fixing Missing Tables for Featured Products API\n";
echo "=================================================\n\n";

try {
    // Check which tables are missing
    $requiredTables = [
        'product_colors',
        'product_sizes', 
        'product_color_sizes'
    ];
    
    $missingTables = [];
    
    echo "1. Checking required tables...\n";
    foreach ($requiredTables as $table) {
        if (Schema::hasTable($table)) {
            echo "✅ Table '$table' exists\n";
        } else {
            echo "❌ Table '$table' is missing\n";
            $missingTables[] = $table;
        }
    }
    
    if (empty($missingTables)) {
        echo "\n✅ All required tables exist!\n";
        echo "🔍 The 500 error might be caused by something else.\n";
        echo "🔧 Try running the diagnosis script: php diagnose_500_error.php\n";
        exit(0);
    }
    
    echo "\n2. Creating missing tables...\n";
    
    // Create product_colors table
    if (in_array('product_colors', $missingTables)) {
        echo "📋 Creating product_colors table...\n";
        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('color_code', 10)->nullable();
            $table->string('image')->nullable();
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('display_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
        echo "✅ Created product_colors table\n";
    }
    
    // Create product_sizes table
    if (in_array('product_sizes', $missingTables)) {
        echo "📋 Creating product_sizes table...\n";
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('standardized_size_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('value')->nullable();
            $table->text('additional_info')->nullable();
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('display_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
        echo "✅ Created product_sizes table\n";
    }
    
    // Create product_color_sizes table
    if (in_array('product_color_sizes', $missingTables)) {
        echo "📋 Creating product_color_sizes table...\n";
        Schema::create('product_color_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_color_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_size_id')->constrained()->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['product_id', 'product_color_id', 'product_size_id'], 'unique_product_color_size');
        });
        echo "✅ Created product_color_sizes table\n";
    }
    
    echo "\n3. Testing the API query after table creation...\n";
    
    // Test the query that was failing
    $query = \App\Models\Product::with([
        'branch',
        'category',
        'colors',
        'sizes',
        'colorSizes.color',
        'colorSizes.size'
    ])->where('featured', true);
    
    echo "📊 Testing query execution...\n";
    $products = $query->paginate(10);
    echo "✅ Query executed successfully!\n";
    echo "📊 Results: " . $products->count() . " products\n";
    
    echo "\n4. Testing API endpoint...\n";
    
    // Test the actual endpoint
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/products?featured=true');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ Connection error: $error\n";
        echo "🔧 Make sure Laravel server is running: php artisan serve --host=0.0.0.0 --port=8000\n";
    } else {
        echo "📊 HTTP Status: $httpCode\n";
        
        if ($httpCode === 200) {
            echo "✅ API endpoint is working!\n";
            $data = json_decode($response, true);
            if ($data && isset($data['success']) && $data['success']) {
                echo "✅ API returned successful response\n";
                if (isset($data['products']['data'])) {
                    echo "📊 Products returned: " . count($data['products']['data']) . "\n";
                }
            }
        } elseif ($httpCode === 500) {
            echo "❌ Still getting 500 error\n";
            echo "📋 Response: " . substr($response, 0, 200) . "...\n";
            echo "🔧 Check Laravel logs for more details\n";
        } else {
            echo "❌ Unexpected status: $httpCode\n";
        }
    }
    
    echo "\n🎉 Table creation completed!\n";
    echo "📋 Summary:\n";
    echo "- Created " . count($missingTables) . " missing tables\n";
    echo "- API query test: ✅ Successful\n";
    
    echo "\n📋 Next steps:\n";
    echo "1. Start Laravel server: php artisan serve --host=0.0.0.0 --port=8000\n";
    echo "2. Test Flutter app connection\n";
    echo "3. If still getting 500 error, check Laravel logs\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
