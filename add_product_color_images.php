<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Product;
use App\Models\ProductColor;

// Initialize database connection
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'marketplace_windsurf',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🎨 Starting Product Color Images Addition Process...\n\n";

// Define color mappings with hex codes
$colorMappings = [
    'black' => ['name' => 'Black', 'hex' => '#000000'],
    'blue' => ['name' => 'Blue', 'hex' => '#0000FF'],
    'red' => ['name' => 'Red', 'hex' => '#FF0000'],
    'white' => ['name' => 'White', 'hex' => '#FFFFFF'],
    'green' => ['name' => 'Green', 'hex' => '#008000'],
    'gray' => ['name' => 'Gray', 'hex' => '#808080'],
    'brown' => ['name' => 'Brown', 'hex' => '#A52A2A'],
    'orange' => ['name' => 'Orange', 'hex' => '#FFA500'],
    'yellow' => ['name' => 'Yellow', 'hex' => '#FFFF00'],
    'violet' => ['name' => 'Violet', 'hex' => '#8A2BE2'],
    'pink' => ['name' => 'Pink', 'hex' => '#FFC0CB'],
    'cyan' => ['name' => 'Cyan', 'hex' => '#00FFFF'],
    'purple' => ['name' => 'Purple', 'hex' => '#800080'],
    'darkblue' => ['name' => 'DarkBlue', 'hex' => '#00008B'],
    'sky' => ['name' => 'SkyBlue', 'hex' => '#87CEEB'],
    'lightgreen' => ['name' => 'LightGreen', 'hex' => '#90EE90'],
    'darkorange' => ['name' => 'DarkOrange', 'hex' => '#FF8C00'],
    'gold' => ['name' => 'Gold', 'hex' => '#FFD700'],
    'darkred' => ['name' => 'DarkRed', 'hex' => '#8B0000'],
    'bluedark' => ['name' => 'DarkBlue', 'hex' => '#00008B'],
    'bluegray' => ['name' => 'BlueGray', 'hex' => '#6A5ACD'],
    'lightblue' => ['name' => 'LightBlue', 'hex' => '#ADD8E6'],
    'lightyellow' => ['name' => 'LightYellow', 'hex' => '#FFFFE0'],
    'bpink' => ['name' => 'BrightPink', 'hex' => '#FF1493'],
    'likered' => ['name' => 'LightRed', 'hex' => '#FF6B6B'],
    'likeblue' => ['name' => 'LightBlue', 'hex' => '#87CEFA'],
    'lightggreen' => ['name' => 'LightGreen', 'hex' => '#90EE90'],
    'yellowcoo' => ['name' => 'CoolYellow', 'hex' => '#FFEB3B'],
];

// Get the products images directory
$imagesDir = __DIR__ . '/Products images';

if (!is_dir($imagesDir)) {
    echo "❌ Products images directory not found: $imagesDir\n";
    exit(1);
}

// Scan for available images
echo "📁 Scanning for available product images...\n";
$imageFiles = glob($imagesDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
echo "Found " . count($imageFiles) . " image files\n\n";

// Group images by product name and color
$productImages = [];
foreach ($imageFiles as $imagePath) {
    $filename = basename($imagePath);
    $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
    
    // Parse filename to extract product name and color
    // Expected format: "Product Name color.jpg" or "Prefix Product Name color.jpg"
    $parts = explode(' ', $filenameWithoutExt);
    
    if (count($parts) >= 2) {
        $color = strtolower(array_pop($parts)); // Last part is color
        $productName = implode(' ', $parts); // Rest is product name
        
        // Clean up product name (remove prefixes like "Classic", "Premium", etc.)
        $productName = preg_replace('/^(Classic|Premium|sic)\s+/i', '', $productName);
        
        if (!isset($productImages[$productName])) {
            $productImages[$productName] = [];
        }
        
        $productImages[$productName][$color] = $filename;
    }
}

echo "📊 Grouped images by product:\n";
foreach ($productImages as $productName => $colors) {
    echo "  • $productName: " . implode(', ', array_keys($colors)) . "\n";
}
echo "\n";

// Get all products from database
$products = Product::all();
echo "🛍️ Found " . $products->count() . " products in database\n\n";

$processedCount = 0;
$skippedCount = 0;

foreach ($products as $product) {
    echo "🔍 Processing product: {$product->name} (ID: {$product->id})\n";
    
    // Check if product already has colors
    $existingColors = ProductColor::where('product_id', $product->id)->count();
    if ($existingColors > 0) {
        echo "  ⏭️ Skipping - already has $existingColors color(s)\n\n";
        $skippedCount++;
        continue;
    }
    
    // Find matching images for this product
    $matchingImages = [];
    foreach ($productImages as $imageProdName => $colors) {
        // Check for exact match or partial match
        if (stripos($product->name, $imageProdName) !== false || 
            stripos($imageProdName, $product->name) !== false ||
            levenshtein(strtolower($product->name), strtolower($imageProdName)) <= 3) {
            $matchingImages = $colors;
            echo "  ✅ Found matching images for pattern: $imageProdName\n";
            break;
        }
    }
    
    if (empty($matchingImages)) {
        echo "  ❌ No matching images found\n\n";
        continue;
    }
    
    // Add color variants
    $colorIndex = 0;
    foreach ($matchingImages as $colorKey => $imageFilename) {
        $colorData = $colorMappings[$colorKey] ?? ['name' => ucfirst($colorKey), 'hex' => '#808080'];
        
        // Copy image to storage directory
        $sourceImagePath = $imagesDir . '/' . $imageFilename;
        $storageImagePath = 'product_images/' . $imageFilename;
        $fullStorageImagePath = __DIR__ . '/storage/app/public/' . $storageImagePath;
        
        // Ensure storage directory exists
        $storageDir = dirname($fullStorageImagePath);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        
        // Copy image
        if (copy($sourceImagePath, $fullStorageImagePath)) {
            echo "  📁 Copied image: $imageFilename\n";
        } else {
            echo "  ❌ Failed to copy image: $imageFilename\n";
            continue;
        }
        
        // Create color record
        $productColor = ProductColor::create([
            'product_id' => $product->id,
            'name' => $colorData['name'],
            'color_code' => $colorData['hex'],
            'image' => $storageImagePath,
            'price_adjustment' => 0,
            'stock' => rand(10, 50),
            'display_order' => $colorIndex,
            'is_default' => $colorIndex === 0, // First color is default
        ]);
        
        echo "  🎨 Added color: {$colorData['name']} ({$colorData['hex']}) - Stock: {$productColor->stock}\n";
        $colorIndex++;
    }
    
    echo "  ✅ Added $colorIndex color variant(s) to {$product->name}\n\n";
    $processedCount++;
}

echo "🎉 Process completed!\n";
echo "📊 Summary:\n";
echo "  • Products processed: $processedCount\n";
echo "  • Products skipped (already have colors): $skippedCount\n";
echo "  • Total products: " . $products->count() . "\n";

$totalColors = ProductColor::count();
echo "  • Total color variants in database: $totalColors\n";

echo "\n✅ Product color images addition completed successfully!\n";
