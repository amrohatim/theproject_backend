<?php

// This script diagnoses and fixes image display issues in the admin and vendor dashboards

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\Service;
use App\Models\Branch;
use App\Models\Category;
use App\Helpers\ImageHelper;

echo "Starting dashboard image fix script...\n";

// 1. Check and fix storage link
echo "\nCHECKING STORAGE LINK:\n";
$storageLink = public_path('storage');
$storagePath = storage_path('app/public');

if (!file_exists($storageLink)) {
    echo "Storage link doesn't exist. Creating it...\n";
    
    // Create the storage link
    try {
        symlink($storagePath, $storageLink);
        echo "✓ Storage link created successfully.\n";
    } catch (\Exception $e) {
        echo "✗ Failed to create storage link: " . $e->getMessage() . "\n";
        echo "  Trying artisan command instead...\n";
        
        exec('php artisan storage:link', $output, $returnCode);
        if ($returnCode === 0) {
            echo "✓ Storage link created successfully using artisan command.\n";
        } else {
            echo "✗ Failed to create storage link using artisan command.\n";
            echo "  Error: " . implode("\n", $output) . "\n";
        }
    }
} else {
    if (is_link($storageLink)) {
        $target = readlink($storageLink);
        echo "✓ Storage link exists and points to: $target\n";
        
        // Check if the link points to the correct location
        if (realpath($target) === realpath($storagePath)) {
            echo "✓ Link target is correct.\n";
        } else {
            echo "✗ Link target is INCORRECT. Expected: $storagePath\n";
            echo "  Fixing link...\n";
            
            // Remove the existing link
            unlink($storageLink);
            
            // Create a new link
            symlink($storagePath, $storageLink);
            echo "✓ Storage link fixed.\n";
        }
    } else {
        echo "✗ Storage path exists but is NOT a symbolic link!\n";
        echo "  Removing and recreating...\n";
        
        // Remove the existing file/directory
        if (is_dir($storageLink)) {
            File::deleteDirectory($storageLink);
        } else {
            unlink($storageLink);
        }
        
        // Create a new link
        symlink($storagePath, $storageLink);
        echo "✓ Storage link recreated.\n";
    }
}

// 2. Check and fix .env configuration
echo "\nCHECKING ENV CONFIGURATION:\n";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Check APP_URL
    $appUrl = Config::get('app.url');
    echo "Current APP_URL: $appUrl\n";
    
    // Determine the correct APP_URL based on the server
    $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $serverPort = $_SERVER['SERVER_PORT'] ?? '8000';
    $correctAppUrl = "http://{$serverName}:{$serverPort}";
    
    if ($appUrl !== $correctAppUrl) {
        echo "Updating APP_URL in .env to $correctAppUrl\n";
        $envContent = preg_replace('/APP_URL=.*/', "APP_URL={$correctAppUrl}", $envContent);
    } else {
        echo "✓ APP_URL is correctly set.\n";
    }
    
    // Check FILESYSTEM_DISK
    if (strpos($envContent, 'FILESYSTEM_DISK=public') === false) {
        echo "Updating FILESYSTEM_DISK in .env to public\n";
        if (strpos($envContent, 'FILESYSTEM_DISK=') !== false) {
            $envContent = preg_replace('/FILESYSTEM_DISK=.*/', 'FILESYSTEM_DISK=public', $envContent);
        } else {
            $envContent .= "\nFILESYSTEM_DISK=public\n";
        }
    } else {
        echo "✓ FILESYSTEM_DISK is correctly set to public\n";
    }
    
    file_put_contents($envPath, $envContent);
}

// 3. Clear configuration cache
echo "\nCLEARING CONFIGURATION CACHE:\n";
exec('php artisan config:clear', $output, $returnCode);
if ($returnCode === 0) {
    echo "✓ Configuration cache cleared successfully.\n";
} else {
    echo "✗ Failed to clear configuration cache.\n";
}

// 4. Check image paths in the database
echo "\nCHECKING IMAGE PATHS IN DATABASE:\n";

// Check product images
$products = Product::all();
echo "Found " . $products->count() . " products.\n";

$fixedProductImages = 0;
foreach ($products as $product) {
    $originalImage = $product->getRawOriginal('image');
    
    if ($originalImage) {
        // Normalize the path
        $normalizedPath = ltrim($originalImage, '/');
        
        // Check if the image exists
        $imagePath = public_path($normalizedPath);
        $storageImagePath = storage_path('app/public/' . basename($normalizedPath));
        
        if (!file_exists($imagePath) && file_exists($storageImagePath)) {
            // Fix the path to ensure it starts with /storage/
            if (!str_starts_with($normalizedPath, 'storage/')) {
                $newPath = 'storage/products/' . basename($normalizedPath);
                
                // Update the product
                $product->image = $newPath;
                $product->save();
                
                echo "Fixed product image path: {$originalImage} -> {$newPath}\n";
                $fixedProductImages++;
            }
        }
    }
}
echo "Fixed {$fixedProductImages} product image paths.\n";

// Check product color images
$productColors = ProductColor::all();
echo "Found " . $productColors->count() . " product colors.\n";

$fixedColorImages = 0;
foreach ($productColors as $color) {
    $originalImage = $color->getRawOriginal('image');
    
    if ($originalImage) {
        // Normalize the path
        $normalizedPath = ltrim($originalImage, '/');
        
        // Check if the image exists
        $imagePath = public_path($normalizedPath);
        $storageImagePath = storage_path('app/public/' . basename($normalizedPath));
        
        if (!file_exists($imagePath) && file_exists($storageImagePath)) {
            // Fix the path to ensure it starts with /storage/
            if (!str_starts_with($normalizedPath, 'storage/')) {
                $newPath = 'storage/product-colors/' . basename($normalizedPath);
                
                // Update the color
                $color->image = $newPath;
                $color->save();
                
                echo "Fixed product color image path: {$originalImage} -> {$newPath}\n";
                $fixedColorImages++;
            }
        }
    }
}
echo "Fixed {$fixedColorImages} product color image paths.\n";

// 5. Create necessary directories
echo "\nCREATING NECESSARY DIRECTORIES:\n";
$directories = [
    public_path('storage/products'),
    public_path('storage/product-colors'),
    public_path('storage/services'),
    public_path('storage/categories'),
    public_path('storage/branches'),
    public_path('storage/users'),
    public_path('storage/deals'),
    public_path('images/products'),
    public_path('images/services'),
    public_path('images/categories'),
];

foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        try {
            mkdir($directory, 0755, true);
            echo "✓ Created directory: {$directory}\n";
        } catch (\Exception $e) {
            echo "✗ Failed to create directory {$directory}: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✓ Directory already exists: {$directory}\n";
    }
}

echo "\nDashboard image fix script completed!\n";
echo "Please restart your Laravel server for changes to take effect.\n";
