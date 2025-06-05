<?php

// This script provides comprehensive diagnostics for product image issues

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "=== PRODUCT IMAGE DIAGNOSTIC TOOL ===\n\n";

// 1. Check storage link
echo "CHECKING STORAGE LINK:\n";
$storageLink = public_path('storage');
if (file_exists($storageLink)) {
    if (is_link($storageLink)) {
        $target = readlink($storageLink);
        echo "✓ Storage link exists and points to: $target\n";
        
        $expected = storage_path('app/public');
        if (realpath($target) === realpath($expected)) {
            echo "✓ Link target is correct\n";
        } else {
            echo "✗ Link target is INCORRECT. Expected: $expected\n";
            echo "  Fixing link...\n";
            unlink($storageLink);
            symlink($expected, $storageLink);
            echo "  Link recreated.\n";
        }
    } else {
        echo "✗ Storage path exists but is NOT a symbolic link!\n";
        echo "  Removing and recreating...\n";
        if (is_dir($storageLink)) {
            File::deleteDirectory($storageLink);
        } else {
            unlink($storageLink);
        }
        symlink(storage_path('app/public'), $storageLink);
        echo "  Link recreated.\n";
    }
} else {
    echo "✗ Storage link does not exist!\n";
    echo "  Creating link...\n";
    symlink(storage_path('app/public'), $storageLink);
    echo "  Link created.\n";
}

// 2. Check directory structure
echo "\nCHECKING DIRECTORY STRUCTURE:\n";
$directories = [
    'public/storage' => public_path('storage'),
    'public/storage/products' => public_path('storage/products'),
    'public/storage/product-colors' => public_path('storage/product-colors'),
    'storage/app/public' => storage_path('app/public'),
    'storage/app/public/products' => storage_path('app/public/products'),
    'storage/app/public/product-colors' => storage_path('app/public/product-colors'),
    'public/images' => public_path('images'),
    'public/images/products' => public_path('images/products'),
    'public/images/product-colors' => public_path('images/product-colors'),
];

foreach ($directories as $name => $path) {
    if (file_exists($path)) {
        echo "✓ $name exists\n";
    } else {
        echo "✗ $name does not exist - creating...\n";
        mkdir($path, 0755, true);
        echo "  Directory created.\n";
    }
}

// 3. Check database records
echo "\nCHECKING DATABASE RECORDS:\n";
$products = Product::with('colors')->get();
echo "Found " . $products->count() . " products in database.\n";

$imagePathPatterns = [
    'starts_with_storage' => 0,
    'starts_with_slash_storage' => 0,
    'starts_with_products' => 0,
    'starts_with_product_colors' => 0,
    'starts_with_images' => 0,
    'starts_with_http' => 0,
    'null_or_empty' => 0,
    'other' => 0
];

$colorImagePathPatterns = [
    'starts_with_storage' => 0,
    'starts_with_slash_storage' => 0,
    'starts_with_products' => 0,
    'starts_with_product_colors' => 0,
    'starts_with_images' => 0,
    'starts_with_http' => 0,
    'null_or_empty' => 0,
    'other' => 0
];

$productsWithMissingImages = 0;
$colorsWithMissingImages = 0;

foreach ($products as $product) {
    $imagePath = $product->getRawOriginal('image');
    
    if (empty($imagePath)) {
        $imagePathPatterns['null_or_empty']++;
        $productsWithMissingImages++;
    } else if (str_starts_with($imagePath, 'storage/')) {
        $imagePathPatterns['starts_with_storage']++;
    } else if (str_starts_with($imagePath, '/storage/')) {
        $imagePathPatterns['starts_with_slash_storage']++;
    } else if (str_starts_with($imagePath, 'products/')) {
        $imagePathPatterns['starts_with_products']++;
    } else if (str_starts_with($imagePath, 'product-colors/')) {
        $imagePathPatterns['starts_with_product_colors']++;
    } else if (str_starts_with($imagePath, 'images/')) {
        $imagePathPatterns['starts_with_images']++;
    } else if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
        $imagePathPatterns['starts_with_http']++;
    } else {
        $imagePathPatterns['other']++;
    }
    
    foreach ($product->colors as $color) {
        $colorImagePath = $color->getRawOriginal('image');
        
        if (empty($colorImagePath)) {
            $colorImagePathPatterns['null_or_empty']++;
            $colorsWithMissingImages++;
        } else if (str_starts_with($colorImagePath, 'storage/')) {
            $colorImagePathPatterns['starts_with_storage']++;
        } else if (str_starts_with($colorImagePath, '/storage/')) {
            $colorImagePathPatterns['starts_with_slash_storage']++;
        } else if (str_starts_with($colorImagePath, 'products/')) {
            $colorImagePathPatterns['starts_with_products']++;
        } else if (str_starts_with($colorImagePath, 'product-colors/')) {
            $colorImagePathPatterns['starts_with_product_colors']++;
        } else if (str_starts_with($colorImagePath, 'images/')) {
            $colorImagePathPatterns['starts_with_images']++;
        } else if (str_starts_with($colorImagePath, 'http://') || str_starts_with($colorImagePath, 'https://')) {
            $colorImagePathPatterns['starts_with_http']++;
        } else {
            $colorImagePathPatterns['other']++;
        }
    }
}

echo "\nProduct image path patterns:\n";
foreach ($imagePathPatterns as $pattern => $count) {
    echo "  $pattern: $count\n";
}

echo "\nColor image path patterns:\n";
foreach ($colorImagePathPatterns as $pattern => $count) {
    echo "  $pattern: $count\n";
}

echo "\nProducts with missing images: $productsWithMissingImages\n";
echo "Colors with missing images: $colorsWithMissingImages\n";

// 4. Check for actual image files
echo "\nCHECKING FOR ACTUAL IMAGE FILES:\n";
$productImagesFound = 0;
$colorImagesFound = 0;

foreach ($products as $product) {
    $imagePath = $product->getRawOriginal('image');
    if (!empty($imagePath)) {
        $filename = basename($imagePath);
        $possiblePaths = [
            public_path("storage/products/$filename"),
            public_path("storage/product-colors/$filename"),
            public_path("images/products/$filename"),
            public_path("images/product-colors/$filename"),
            public_path(ltrim($imagePath, '/')),
            storage_path("app/public/products/$filename"),
            storage_path("app/public/product-colors/$filename"),
            storage_path("app/public/" . ltrim($imagePath, '/storage/')),
        ];
        
        $found = false;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $found = true;
                break;
            }
        }
        
        if ($found) {
            $productImagesFound++;
        }
    }
    
    foreach ($product->colors as $color) {
        $colorImagePath = $color->getRawOriginal('image');
        if (!empty($colorImagePath)) {
            $filename = basename($colorImagePath);
            $possiblePaths = [
                public_path("storage/products/$filename"),
                public_path("storage/product-colors/$filename"),
                public_path("images/products/$filename"),
                public_path("images/product-colors/$filename"),
                public_path(ltrim($colorImagePath, '/')),
                storage_path("app/public/products/$filename"),
                storage_path("app/public/product-colors/$filename"),
                storage_path("app/public/" . ltrim($colorImagePath, '/storage/')),
            ];
            
            $found = false;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $found = true;
                    break;
                }
            }
            
            if ($found) {
                $colorImagesFound++;
            }
        }
    }
}

echo "Product images found: $productImagesFound out of " . ($products->count() - $productsWithMissingImages) . "\n";
echo "Color images found: $colorImagesFound out of " . ($products->sum(function($product) { return $product->colors->count(); }) - $colorsWithMissingImages) . "\n";

// 5. Create a sample image if none exist
echo "\nCREATING SAMPLE IMAGES IF NEEDED:\n";
if ($productImagesFound == 0 || $colorImagesFound == 0) {
    echo "Creating sample images...\n";
    
    // Create a simple image
    $width = 400;
    $height = 300;
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bgColor = imagecolorallocate($image, 240, 240, 240);
    $textColor = imagecolorallocate($image, 50, 50, 50);
    $borderColor = imagecolorallocate($image, 200, 200, 200);
    
    // Fill background
    imagefill($image, 0, 0, $bgColor);
    
    // Add border
    imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);
    
    // Add text
    $text = "Sample Product Image";
    $font = 5; // Built-in font
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    imagestring($image, $font, $x, $y, $text, $textColor);
    
    // Save the image
    $sampleImagePath = public_path('storage/products/sample_product.jpg');
    imagejpeg($image, $sampleImagePath, 90);
    imagedestroy($image);
    
    echo "Sample image created at: $sampleImagePath\n";
    
    // Copy to other locations
    $locations = [
        public_path('storage/product-colors/sample_product.jpg'),
        public_path('images/products/sample_product.jpg'),
        public_path('images/product-colors/sample_product.jpg'),
        storage_path('app/public/products/sample_product.jpg'),
        storage_path('app/public/product-colors/sample_product.jpg'),
    ];
    
    foreach ($locations as $location) {
        copy($sampleImagePath, $location);
        echo "Copied to: $location\n";
    }
    
    // Update a product to use this image
    if ($products->isNotEmpty()) {
        $product = $products->first();
        $product->image = '/storage/products/sample_product.jpg';
        $product->save();
        
        if ($product->colors->isNotEmpty()) {
            $color = $product->colors->first();
            $color->image = '/storage/product-colors/sample_product.jpg';
            $color->is_default = true;
            $color->save();
        }
        
        echo "Updated product ID {$product->id} to use sample images\n";
    }
}

echo "\nDIAGNOSTIC COMPLETE\n";
