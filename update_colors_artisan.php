<?php

use App\Models\Product;
use App\Models\ProductColor;

echo "🔄 Starting product color variants update process...\n\n";

// Comprehensive color mapping with hex codes
$colorMappings = [
    'black' => ['name' => 'Black', 'hex' => '#000000'],
    'bk' => ['name' => 'Black', 'hex' => '#000000'],
    'balck' => ['name' => 'Black', 'hex' => '#000000'], // Handle typo
    'blue' => ['name' => 'Blue', 'hex' => '#0000FF'],
    'bluedark' => ['name' => 'Dark Blue', 'hex' => '#000080'],
    'darkblue' => ['name' => 'Dark Blue', 'hex' => '#000080'],
    'lightblue' => ['name' => 'Light Blue', 'hex' => '#ADD8E6'],
    'light blue' => ['name' => 'Light Blue', 'hex' => '#ADD8E6'],
    'likeblue' => ['name' => 'Light Blue', 'hex' => '#ADD8E6'],
    'bluegray' => ['name' => 'Blue Gray', 'hex' => '#6699CC'],
    'red' => ['name' => 'Red', 'hex' => '#FF0000'],
    'darkred' => ['name' => 'Dark Red', 'hex' => '#8B0000'],
    'likered' => ['name' => 'Light Red', 'hex' => '#FFB6C1'],
    'white' => ['name' => 'White', 'hex' => '#FFFFFF'],
    'green' => ['name' => 'Green', 'hex' => '#008000'],
    'lightgreen' => ['name' => 'Light Green', 'hex' => '#90EE90'],
    'lightggreen' => ['name' => 'Light Green', 'hex' => '#90EE90'], // Handle typo
    'light green' => ['name' => 'Light Green', 'hex' => '#90EE90'],
    'gray' => ['name' => 'Gray', 'hex' => '#808080'],
    'grey' => ['name' => 'Gray', 'hex' => '#808080'],
    'brown' => ['name' => 'Brown', 'hex' => '#A52A2A'],
    'orange' => ['name' => 'Orange', 'hex' => '#FFA500'],
    'darkorange' => ['name' => 'Dark Orange', 'hex' => '#FF8C00'],
    'yellow' => ['name' => 'Yellow', 'hex' => '#FFFF00'],
    'lightyellow' => ['name' => 'Light Yellow', 'hex' => '#FFFFE0'],
    'yellowcoo' => ['name' => 'Cool Yellow', 'hex' => '#FFFACD'],
    'violet' => ['name' => 'Violet', 'hex' => '#8A2BE2'],
    'pink' => ['name' => 'Pink', 'hex' => '#FFC0CB'],
    'bpink' => ['name' => 'Bright Pink', 'hex' => '#FF1493'],
    'cyan' => ['name' => 'Cyan', 'hex' => '#00FFFF'],
    'purple' => ['name' => 'Purple', 'hex' => '#800080'],
    'gold' => ['name' => 'Gold', 'hex' => '#FFD700'],
    'sky' => ['name' => 'Sky Blue', 'hex' => '#87CEEB'],
];

// Get images directory
$imagesDir = base_path('Products images');
if (!is_dir($imagesDir)) {
    echo "❌ Products images directory not found at: {$imagesDir}\n";
    return;
}

// Get all image files
$imageFiles = glob($imagesDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
echo "📁 Found " . count($imageFiles) . " image files\n\n";

// Create storage directory if it doesn't exist
$storageDir = storage_path('app/public/product_images');
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
    echo "📁 Created storage directory: {$storageDir}\n";
}

try {
    // Get all products with their existing colors
    $products = Product::with('colors')->whereHas('colors')->get();
    
    if ($products->isEmpty()) {
        echo "❌ No products with color variants found in database.\n";
        return;
    }
    
    echo "🔍 Found " . $products->count() . " products with color variants\n\n";
    
    $updatedCount = 0;
    $matchedImages = 0;
    $errors = [];
    
    foreach ($products as $product) {
        echo "🏷️  Processing product: {$product->name} (ID: {$product->id})\n";
        
        // Find matching images for this product
        $productImages = [];
        foreach ($imageFiles as $imagePath) {
            $filename = basename($imagePath);
            
            // Check if image filename contains the product name
            if (stripos($filename, $product->name) !== false) {
                $productImages[] = $filename;
            }
        }
        
        if (empty($productImages)) {
            echo "   ⚠️  No matching images found for product: {$product->name}\n";
            continue;
        }
        
        echo "   📸 Found " . count($productImages) . " matching image(s)\n";
        
        // Create a mapping of available colors from images
        $availableColors = [];
        foreach ($productImages as $imageFilename) {
            $filenameLower = strtolower($imageFilename);
            $productNameLower = strtolower($product->name);
            
            // Remove product name and extension to get color part
            $colorPart = str_replace($productNameLower, '', $filenameLower);
            $colorPart = preg_replace('/\.(jpg|jpeg|png|gif)$/', '', $colorPart);
            $colorPart = trim($colorPart, ' .-_');
            
            // Check if this color part matches any known color
            foreach ($colorMappings as $colorKey => $colorData) {
                if ($colorPart === $colorKey) {
                    $availableColors[$colorKey] = [
                        'filename' => $imageFilename,
                        'data' => $colorData
                    ];
                    break;
                }
            }
        }
        
        echo "   🎨 Available colors from images: " . implode(', ', array_keys($availableColors)) . "\n";
        
        // Process existing color variants
        foreach ($product->colors as $color) {
            echo "      🔍 Processing existing color: {$color->name}\n";
            
            $updated = false;
            
            // Try to match with available colors
            foreach ($availableColors as $colorKey => $colorInfo) {
                $colorData = $colorInfo['data'];
                
                // Check if current color matches this available color
                if (strtolower($color->name) === strtolower($colorData['name']) || 
                    strtolower($color->name) === $colorKey) {
                    
                    echo "         ✅ Found matching image: {$colorInfo['filename']}\n";
                    
                    // Copy image to storage
                    $sourceImagePath = $imagesDir . '/' . $colorInfo['filename'];
                    $storageImagePath = 'product_images/' . $colorInfo['filename'];
                    $fullStorageImagePath = $storageDir . '/' . $colorInfo['filename'];
                    
                    if (copy($sourceImagePath, $fullStorageImagePath)) {
                        echo "         📁 Copied image to storage\n";
                        
                        // Update color variant
                        $color->update([
                            'name' => $colorData['name'],
                            'color_code' => $colorData['hex'],
                            'image' => $storageImagePath,
                        ]);
                        
                        echo "         🔄 Updated color: {$colorData['name']} ({$colorData['hex']})\n";
                        $updatedCount++;
                        $matchedImages++;
                        $updated = true;
                        
                        // Remove from available colors to avoid duplicates
                        unset($availableColors[$colorKey]);
                        break;
                    } else {
                        $error = "Failed to copy image: {$colorInfo['filename']}";
                        echo "         ❌ {$error}\n";
                        $errors[] = $error;
                    }
                }
            }
            
            if (!$updated) {
                echo "         ⚠️  No matching image found for color: {$color->name}\n";
            }
        }
        
        echo "\n";
    }
    
    echo "📊 Update Summary:\n";
    echo "   - Products processed: " . $products->count() . "\n";
    echo "   - Color variants updated: {$updatedCount}\n";
    echo "   - Images matched and copied: {$matchedImages}\n";
    echo "   - Errors: " . count($errors) . "\n";
    
    if (!empty($errors)) {
        echo "\n❌ Errors encountered:\n";
        foreach ($errors as $error) {
            echo "   - {$error}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    return;
}

echo "\n✅ Product color variants update completed!\n";
