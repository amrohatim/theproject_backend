<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Product;
use App\Models\ProductColor;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database configuration
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'database' => $_ENV['DB_DATABASE'] ?? 'marketplace_db',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🔍 Checking current products and color variants...\n\n";

try {
    // Get all products with their colors
    $products = Product::with('colors')->get();
    
    if ($products->isEmpty()) {
        echo "❌ No products found in database.\n";
        exit(1);
    }
    
    echo "📊 Found " . $products->count() . " products in database:\n\n";
    
    $productsWithColors = 0;
    $totalColors = 0;
    
    foreach ($products as $product) {
        echo "🏷️  Product: {$product->name} (ID: {$product->id})\n";
        
        if ($product->colors->isEmpty()) {
            echo "   ❌ No color variants\n";
        } else {
            $productsWithColors++;
            echo "   ✅ " . $product->colors->count() . " color variant(s):\n";
            
            foreach ($product->colors as $color) {
                $totalColors++;
                $imageStatus = $color->image ? "✅ {$color->image}" : "❌ No image";
                echo "      - {$color->name} ({$color->color_code}) - {$imageStatus}\n";
            }
        }
        echo "\n";
    }
    
    echo "📈 Summary:\n";
    echo "   - Total products: " . $products->count() . "\n";
    echo "   - Products with colors: {$productsWithColors}\n";
    echo "   - Total color variants: {$totalColors}\n";
    
    // Check available images in the Products images directory
    echo "\n🖼️  Checking available product images...\n";
    $imagesDir = __DIR__ . '/Products images';
    
    if (!is_dir($imagesDir)) {
        echo "❌ Products images directory not found at: {$imagesDir}\n";
        exit(1);
    }
    
    $imageFiles = glob($imagesDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    echo "📁 Found " . count($imageFiles) . " image files in Products images directory\n\n";
    
    // Show first 10 images as examples
    echo "📋 Sample image files:\n";
    foreach (array_slice($imageFiles, 0, 10) as $imagePath) {
        $filename = basename($imagePath);
        echo "   - {$filename}\n";
    }
    
    if (count($imageFiles) > 10) {
        echo "   ... and " . (count($imageFiles) - 10) . " more files\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ Database check completed!\n";
