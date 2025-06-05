<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "🔧 Marking Products as Featured\n";
echo "==============================\n\n";

try {
    $totalProducts = Product::count();
    echo "📊 Total products in database: $totalProducts\n";
    
    if ($totalProducts == 0) {
        echo "❌ No products found in database\n";
        echo "🔧 You need to add some products first\n";
        exit(1);
    }
    
    $featuredCount = Product::where('featured', true)->count();
    echo "📊 Currently featured products: $featuredCount\n";
    
    if ($featuredCount == 0) {
        echo "\n🔧 Marking first 5 products as featured...\n";
        
        $updated = Product::where('is_available', true)
            ->limit(5)
            ->update(['featured' => true]);
        
        echo "✅ Marked $updated products as featured\n";
        
        $newFeaturedCount = Product::where('featured', true)->count();
        echo "📊 Featured products after update: $newFeaturedCount\n";
        
        // Show the featured products
        $featuredProducts = Product::where('featured', true)->get(['id', 'name', 'price']);
        echo "\n📋 Featured products:\n";
        foreach ($featuredProducts as $product) {
            echo "  - ID: {$product->id}, Name: {$product->name}, Price: \${$product->price}\n";
        }
    } else {
        echo "✅ Already have featured products\n";
    }
    
    echo "\n🎉 Ready to test the API!\n";
    echo "📋 Next steps:\n";
    echo "1. Run: php debug_500_error.php\n";
    echo "2. Run: php test_api_curl.php\n";
    echo "3. Start server: php artisan serve --host=0.0.0.0 --port=8000\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
