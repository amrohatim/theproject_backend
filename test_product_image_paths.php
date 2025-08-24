<?php

// This script tests product image paths to help diagnose display issues

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

// Enable debug logging
Log::info('Starting product image path test');

// Get a few products to test
$products = Product::with('colors')->take(5)->get();

if ($products->isEmpty()) {
    echo "No products found in the database.\n";
    exit;
}

echo "Testing " . $products->count() . " products:\n\n";

foreach ($products as $product) {
    echo "Product ID: {$product->id}, Name: {$product->name}\n";
    echo "Raw Image Path: " . ($product->getRawOriginal('image') ?? 'null') . "\n";
    echo "Processed Image URL: {$product->image}\n";
    
    // Check if the image file exists
    $imagePath = $product->getRawOriginal('image');
    if ($imagePath) {
        // Check various possible locations
        $filename = basename($imagePath);
        $possiblePaths = [
            public_path("images/products/{$filename}"),
            public_path("storage/products/{$filename}"),
            public_path(ltrim($imagePath, '/')),
            public_path("storage/{$filename}"),
            public_path($filename),
            public_path("storage/product-colors/{$filename}"),
            public_path("images/product-colors/{$filename}"),
            storage_path("app/public/{$filename}"),
            storage_path("app/public/products/{$filename}"),
            storage_path("app/public/product-colors/{$filename}"),
        ];
        
        $fileExists = false;
        $existingPaths = [];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $fileExists = true;
                $existingPaths[] = $path;
            }
        }
        
        if ($fileExists) {
            echo "Image file exists at:\n";
            foreach ($existingPaths as $path) {
                echo "  - {$path}\n";
            }
        } else {
            echo "Image file does not exist in any expected location.\n";
        }
    } else {
        echo "No image path stored for this product.\n";
    }
    
    // Check colors
    echo "\nColors:\n";
    foreach ($product->colors as $color) {
        echo "  Color ID: {$color->id}, Name: {$color->name}, Is Default: " . ($color->is_default ? 'Yes' : 'No') . "\n";
        echo "  Raw Image Path: " . ($color->getRawOriginal('image') ?? 'null') . "\n";
        echo "  Processed Image URL: {$color->image}\n";
        
        // Check if the color image file exists
        $colorImagePath = $color->getRawOriginal('image');
        if ($colorImagePath) {
            // Check various possible locations
            $filename = basename($colorImagePath);
            $possiblePaths = [
                public_path("images/products/{$filename}"),
                public_path("storage/products/{$filename}"),
                public_path(ltrim($colorImagePath, '/')),
                public_path("storage/{$filename}"),
                public_path($filename),
                public_path("storage/product-colors/{$filename}"),
                public_path("images/product-colors/{$filename}"),
                storage_path("app/public/{$filename}"),
                storage_path("app/public/products/{$filename}"),
                storage_path("app/public/product-colors/{$filename}"),
            ];
            
            $fileExists = false;
            $existingPaths = [];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $fileExists = true;
                    $existingPaths[] = $path;
                }
            }
            
            if ($fileExists) {
                echo "  Color image file exists at:\n";
                foreach ($existingPaths as $path) {
                    echo "    - {$path}\n";
                }
            } else {
                echo "  Color image file does not exist in any expected location.\n";
            }
        } else {
            echo "  No image path stored for this color.\n";
        }
        
        echo "\n";
    }
    
    echo "----------------------------------------\n\n";
}

// Check storage link
if (file_exists(public_path('storage'))) {
    echo "Storage symbolic link exists.\n";
    $target = readlink(public_path('storage'));
    echo "Target: {$target}\n";
    
    $expected = storage_path('app/public');
    if ($target === $expected) {
        echo "Symbolic link is correctly configured.\n";
    } else {
        echo "Warning: Symbolic link points to incorrect location.\n";
        echo "Expected: {$expected}\n";
    }
} else {
    echo "Storage symbolic link does not exist.\n";
}

// Check directory existence
$directories = [
    public_path('storage/products'),
    public_path('storage/product-colors'),
    public_path('images/products'),
    public_path('images/product-colors'),
    storage_path('app/public/products'),
    storage_path('app/public/product-colors')
];

echo "\nDirectory status:\n";
foreach ($directories as $dir) {
    echo "{$dir}: " . (file_exists($dir) ? "Exists" : "Does not exist") . "\n";
}

echo "\nTest completed.\n";
