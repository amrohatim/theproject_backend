<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Services\DealService;
use Illuminate\Support\Facades\DB;

echo "🔍 Debugging 500 Internal Server Error\n";
echo "=====================================\n\n";

try {
    echo "1. Testing basic database queries...\n";
    
    // Test basic product count
    $totalProducts = Product::count();
    echo "✅ Basic query works - Total products: $totalProducts\n";
    
    // Test featured filter
    $featuredCount = Product::where('featured', true)->count();
    echo "✅ Featured filter works - Featured products: $featuredCount\n";
    
    // Test availability filter
    $availableCount = Product::where('is_available', true)->count();
    echo "✅ Availability filter works - Available products: $availableCount\n";
    
    echo "\n2. Testing individual relationships...\n";
    
    // Test each relationship one by one
    $relationships = [
        'branch' => 'branches',
        'category' => 'categories', 
        'colors' => 'product_colors',
        'sizes' => 'product_sizes',
        'colorSizes' => 'product_color_sizes'
    ];
    
    foreach ($relationships as $relation => $table) {
        try {
            $query = Product::with([$relation])->where('featured', true)->limit(1);
            $result = $query->get();
            echo "✅ Relationship '$relation' works\n";
        } catch (Exception $e) {
            echo "❌ Relationship '$relation' failed: " . $e->getMessage() . "\n";
            
            // Check if the table exists
            $tableExists = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
            if (empty($tableExists)) {
                echo "   → Table '$table' does not exist\n";
            } else {
                echo "   → Table '$table' exists but relationship has issues\n";
            }
        }
    }
    
    echo "\n3. Testing nested relationship (colorSizes.color, colorSizes.size)...\n";
    
    try {
        $query = Product::with(['colorSizes.color'])->where('featured', true)->limit(1);
        $result = $query->get();
        echo "✅ Nested relationship 'colorSizes.color' works\n";
    } catch (Exception $e) {
        echo "❌ Nested relationship 'colorSizes.color' failed: " . $e->getMessage() . "\n";
    }
    
    try {
        $query = Product::with(['colorSizes.size'])->where('featured', true)->limit(1);
        $result = $query->get();
        echo "✅ Nested relationship 'colorSizes.size' works\n";
    } catch (Exception $e) {
        echo "❌ Nested relationship 'colorSizes.size' failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Testing the exact controller query...\n";
    
    try {
        // This is the exact query from ProductController@index
        $query = Product::with([
            'branch',
            'category',
            'colors',
            'sizes',
            'colorSizes.color',
            'colorSizes.size'
        ]);
        
        // Apply featured filter
        $query->where('featured', true);
        
        echo "📊 Query SQL: " . $query->toSql() . "\n";
        echo "📊 Query bindings: " . json_encode($query->getBindings()) . "\n";
        
        // Try to execute
        $products = $query->paginate(10);
        echo "✅ Controller query executed successfully!\n";
        echo "📊 Results: " . $products->count() . " products\n";
        
    } catch (Exception $e) {
        echo "❌ Controller query failed: " . $e->getMessage() . "\n";
        echo "📋 This is the exact cause of the 500 error!\n";
        
        // Get more details about the error
        echo "📋 Error details:\n";
        echo "   - Message: " . $e->getMessage() . "\n";
        echo "   - File: " . $e->getFile() . "\n";
        echo "   - Line: " . $e->getLine() . "\n";
        
        return;
    }
    
    echo "\n5. Testing DealService dependency...\n";
    
    try {
        // Check if DealService exists and works
        $dealService = app(DealService::class);
        echo "✅ DealService can be instantiated\n";
        
        // Test with a sample product if available
        if ($totalProducts > 0) {
            $sampleProduct = Product::first();
            $dealInfo = $dealService->calculateDiscountedPrice($sampleProduct);
            echo "✅ DealService calculateDiscountedPrice works\n";
        }
        
    } catch (Exception $e) {
        echo "❌ DealService failed: " . $e->getMessage() . "\n";
        echo "📋 This might be causing the 500 error in the controller!\n";
    }
    
    echo "\n6. Testing controller transformation logic...\n";
    
    try {
        if ($totalProducts > 0) {
            $product = Product::with([
                'branch',
                'category',
                'colors',
                'sizes',
                'colorSizes.color',
                'colorSizes.size'
            ])->first();
            
            if ($product) {
                // Test branch name access
                $branchName = $product->branch ? $product->branch->name : 'No branch';
                echo "✅ Branch name access works: $branchName\n";
                
                // Test deal service
                $dealService = app(DealService::class);
                $dealInfo = $dealService->calculateDiscountedPrice($product);
                echo "✅ Deal calculation works\n";
                
                // Test default color image
                $defaultColorImage = $product->getDefaultColorImage();
                echo "✅ Default color image method works\n";
                
                // Test color-size combinations
                $colorSizeCombinations = [];
                foreach ($product->colorSizes as $colorSize) {
                    $colorSizeCombinations[] = [
                        'id' => $colorSize->id,
                        'color_name' => $colorSize->color->name ?? 'Unknown',
                        'size_name' => $colorSize->size->name ?? 'Unknown',
                    ];
                }
                echo "✅ Color-size transformation works\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Controller transformation failed: " . $e->getMessage() . "\n";
        echo "📋 This is likely causing the 500 error!\n";
    }
    
    echo "\n7. Final API simulation...\n";
    
    try {
        // Simulate the complete controller logic
        $query = Product::with([
            'branch',
            'category',
            'colors',
            'sizes',
            'colorSizes.color',
            'colorSizes.size'
        ]);
        
        $query->where('featured', true);
        $products = $query->paginate(10);
        
        // Transform products (like in controller)
        $dealService = app(DealService::class);
        
        $products->getCollection()->transform(function ($product) use ($dealService) {
            $product->branch_name = $product->branch ? $product->branch->name : null;
            
            $dealInfo = $dealService->calculateDiscountedPrice($product);
            $product->has_discount = $dealInfo['has_discount'];
            $product->original_price = $dealInfo['original_price'];
            $product->discounted_price = $dealInfo['discounted_price'];
            $product->discount_percentage = $dealInfo['discount_percentage'];
            $product->discount_amount = $dealInfo['discount_amount'];
            
            if ($dealInfo['deal']) {
                $product->deal = $dealInfo['deal'];
            }
            
            $product->default_color_image = $product->getDefaultColorImage();
            
            $colorSizeCombinations = [];
            foreach ($product->colorSizes as $colorSize) {
                $colorSizeCombinations[] = [
                    'id' => $colorSize->id,
                    'product_id' => $colorSize->product_id,
                    'color_id' => $colorSize->product_color_id,
                    'size_id' => $colorSize->product_size_id,
                    'color_name' => $colorSize->color ? $colorSize->color->name : null,
                    'color_code' => $colorSize->color ? $colorSize->color->color_code : null,
                    'size_name' => $colorSize->size ? $colorSize->size->name : null,
                    'size_value' => $colorSize->size ? $colorSize->size->value : null,
                    'stock' => $colorSize->stock,
                    'price_adjustment' => $colorSize->price_adjustment,
                    'is_available' => $colorSize->is_available,
                ];
            }
            
            $product->color_size_combinations = $colorSizeCombinations;
            
            return $product;
        });
        
        echo "✅ Complete API simulation successful!\n";
        echo "📊 Transformed products: " . $products->count() . "\n";
        
        // Create response array
        $response = [
            'success' => true,
            'products' => $products,
        ];
        
        echo "✅ Response array created successfully!\n";
        echo "🎉 The API should work correctly!\n";
        
    } catch (Exception $e) {
        echo "❌ API simulation failed: " . $e->getMessage() . "\n";
        echo "📋 Error in: " . $e->getFile() . " at line " . $e->getLine() . "\n";
        echo "📋 This is the exact cause of the 500 error!\n";
    }

} catch (Exception $e) {
    echo "❌ Critical error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n📋 Next steps:\n";
echo "1. If all tests pass, start Laravel server and test the actual endpoint\n";
echo "2. If any test fails, that's the root cause of the 500 error\n";
echo "3. Check Laravel logs for more detailed error information\n";
