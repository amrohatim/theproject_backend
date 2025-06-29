<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "🔧 Fixing Featured Products Issue\n";
echo "=================================\n\n";

try {
    // Check total products
    $totalProducts = Product::count();
    echo "📊 Total products in database: $totalProducts\n";

    if ($totalProducts == 0) {
        echo "❌ No products found in database. Please run the seeders first:\n";
        echo "   php artisan db:seed\n";
        exit(1);
    }

    // Check featured products
    $featuredCount = Product::where('featured', true)->count();
    echo "📊 Current featured products: $featuredCount\n\n";

    if ($featuredCount == 0) {
        echo "🔧 No featured products found. Creating featured products...\n";
        
        // Mark first 5 available products as featured
        $updated = Product::where('is_available', true)
            ->limit(5)
            ->update(['featured' => true]);
        
        echo "✅ Marked $updated products as featured\n\n";
        
        $featuredCount = Product::where('featured', true)->count();
        echo "📊 Featured products after update: $featuredCount\n\n";
    }

    // Show featured products details
    echo "📋 Featured Products List:\n";
    echo "-------------------------\n";
    $featured = Product::where('featured', true)
        ->where('is_available', true)
        ->with(['branch', 'category'])
        ->get();

    foreach ($featured as $index => $product) {
        echo ($index + 1) . ". {$product->name}\n";
        echo "   - ID: {$product->id}\n";
        echo "   - Price: \${$product->price}\n";
        echo "   - Branch: " . ($product->branch->name ?? 'N/A') . "\n";
        echo "   - Category: " . ($product->category->name ?? 'N/A') . "\n";
        echo "   - Available: " . ($product->is_available ? 'Yes' : 'No') . "\n";
        echo "   - Featured: " . ($product->featured ? 'Yes' : 'No') . "\n\n";
    }

    // Test API endpoint simulation
    echo "🔍 Testing API Response Simulation:\n";
    echo "-----------------------------------\n";
    
    $apiResponse = [
        'success' => true,
        'products' => $featured->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'featured' => $product->featured,
                'is_available' => $product->is_available,
                'branch_name' => $product->branch->name ?? null,
                'category_name' => $product->category->name ?? null,
            ];
        })->toArray()
    ];
    
    echo "API Response would be:\n";
    echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n\n";

    echo "✅ Featured products setup completed successfully!\n\n";
    
    echo "🚀 Next Steps:\n";
    echo "1. Make sure Laravel server is running: php artisan serve --host=0.0.0.0 --port=8000\n";
    echo "2. Verify the server is accessible at: http://192.168.70.48:8000\n";
    echo "3. Test the API endpoint: http://192.168.70.48:8000/api/products?featured=true\n";
    echo "4. Run the Flutter app and check the debug console for API calls\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
