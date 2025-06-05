<?php

// Simple test to check if the service category images directory exists and has content

$serviceImagePath = __DIR__ . '/app service category images';

echo "Testing Service Category Images Directory\n";
echo "=========================================\n\n";

if (!is_dir($serviceImagePath)) {
    echo "❌ Directory not found: {$serviceImagePath}\n";
    exit(1);
}

echo "✅ Directory found: {$serviceImagePath}\n";

$directories = glob($serviceImagePath . '/*', GLOB_ONLYDIR);

if (empty($directories)) {
    echo "❌ No subdirectories found\n";
    exit(1);
}

echo "✅ Found " . count($directories) . " subdirectories:\n\n";

foreach ($directories as $directory) {
    $categoryName = basename($directory);
    echo "📁 {$categoryName}\n";
    
    $images = glob($directory . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    echo "   📸 " . count($images) . " images found\n";
    
    foreach ($images as $image) {
        echo "      - " . basename($image) . "\n";
    }
    echo "\n";
}

echo "✅ Directory structure test completed successfully!\n";
echo "Ready to run: php artisan db:seed --class=ServiceCategoriesSeeder\n";
