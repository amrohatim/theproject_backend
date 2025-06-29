<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test the seeder logic
echo "Service Categories Seeder Test\n";
echo "==============================\n\n";

$serviceImagePath = base_path('app service category images');

echo "Checking directory: {$serviceImagePath}\n";

if (!File::exists($serviceImagePath)) {
    echo "❌ Service category images directory not found: {$serviceImagePath}\n";
    exit(1);
}

echo "✅ Directory found!\n";

$directories = File::directories($serviceImagePath);

if (empty($directories)) {
    echo "❌ No service category directories found.\n";
    exit(1);
}

echo "✅ Found " . count($directories) . " service category directories:\n\n";

foreach ($directories as $directory) {
    $categoryName = basename($directory);
    echo "📁 Processing: {$categoryName}\n";
    
    $imageFiles = File::files($directory);
    echo "   📸 Found " . count($imageFiles) . " images:\n";
    
    foreach ($imageFiles as $imageFile) {
        echo "      - {$imageFile->getFilename()}\n";
    }
    echo "\n";
}

echo "✅ Directory structure test completed successfully!\n";
echo "\nNow you can run: php artisan db:seed --class=ServiceCategoriesSeeder\n";
