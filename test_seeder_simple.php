<?php

// Simple test without Laravel dependencies
echo "Testing Service Categories Directory Structure\n";
echo "==============================================\n\n";

$serviceImagePath = __DIR__ . '/app service category images';

// Check if directory exists
if (!is_dir($serviceImagePath)) {
    echo "❌ Directory not found: {$serviceImagePath}\n";
    echo "Please ensure the 'app service category images' directory exists.\n";
    exit(1);
}

echo "✅ Found directory: {$serviceImagePath}\n\n";

// Get all subdirectories
$directories = array_filter(glob($serviceImagePath . '/*'), 'is_dir');

if (empty($directories)) {
    echo "❌ No subdirectories found in the service images directory.\n";
    exit(1);
}

echo "✅ Found " . count($directories) . " service category directories:\n\n";

$totalImages = 0;
$categoryData = [];

foreach ($directories as $directory) {
    $categoryName = basename($directory);
    echo "📁 {$categoryName}\n";
    
    // Get all image files
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
    $images = [];
    
    foreach ($imageExtensions as $ext) {
        $images = array_merge($images, glob($directory . '/*.' . $ext));
    }
    
    if (empty($images)) {
        echo "   ⚠️  No images found\n";
        continue;
    }
    
    echo "   📸 " . count($images) . " images found:\n";
    
    $parentImageFound = false;
    $childImages = [];
    
    foreach ($images as $image) {
        $filename = pathinfo($image, PATHINFO_FILENAME);
        echo "      - " . basename($image) . "\n";
        
        if ($filename === $categoryName) {
            $parentImageFound = true;
            echo "        ✅ Parent category image\n";
        } else {
            $childImages[] = $filename;
        }
        $totalImages++;
    }
    
    if (!$parentImageFound) {
        echo "        ⚠️  No parent category image found (expected: {$categoryName}.jpg)\n";
    }
    
    $categoryData[$categoryName] = [
        'parent_image' => $parentImageFound,
        'child_count' => count($childImages),
        'children' => $childImages
    ];
    
    echo "\n";
}

echo "📊 Summary:\n";
echo "   Total directories: " . count($directories) . "\n";
echo "   Total images: {$totalImages}\n";
echo "   Categories with parent images: " . count(array_filter($categoryData, function($data) { return $data['parent_image']; })) . "\n";
echo "   Total child categories: " . array_sum(array_column($categoryData, 'child_count')) . "\n\n";

echo "✅ Directory structure test completed!\n";
echo "Ready to run the seeder: php artisan db:seed --class=ServiceCategoriesSeeder\n";
