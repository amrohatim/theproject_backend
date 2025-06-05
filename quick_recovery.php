<?php

echo "🔄 Quick Recovery Script\n";
echo "========================\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "✅ Laravel bootstrapped successfully\n";
    
    // Check database connection
    $products = \App\Models\Product::count();
    $categories = \App\Models\Category::where('type', 'product')->count();
    $branches = \App\Models\Branch::count();
    
    echo "📊 Current Database State:\n";
    echo "   - Products: {$products}\n";
    echo "   - Categories: {$categories}\n";
    echo "   - Branches: {$branches}\n\n";
    
    // Check product images
    $imageFiles = glob('Products images/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    echo "📸 Product Images Available: " . count($imageFiles) . "\n\n";
    
    if (count($imageFiles) > 0) {
        echo "Sample image files:\n";
        for ($i = 0; $i < min(5, count($imageFiles)); $i++) {
            echo "   - " . basename($imageFiles[$i]) . "\n";
        }
    }
    
    // Recovery status
    echo "\n🎯 Recovery Status:\n";
    if ($products >= 102) {
        echo "✅ SUCCESS! Database has {$products} products (target: 102+)\n";
    } else {
        $needed = 102 - $products;
        echo "⚠️  Need {$needed} more products to reach target of 102+\n";
        echo "   Available images: " . count($imageFiles) . "\n";
        echo "   Can potentially create " . count($imageFiles) . " products\n";
    }
    
    if ($categories >= 20) {
        echo "✅ Categories: {$categories} (comprehensive categories available)\n";
    } else {
        echo "⚠️  Categories: {$categories} (may need more categories)\n";
    }
    
    echo "\n✅ Quick recovery check completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
