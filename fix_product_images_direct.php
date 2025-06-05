<?php

// This script fixes product image issues by ensuring all products have valid image paths

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

echo "=== PRODUCT IMAGE FIXER ===\n\n";

// 1. Ensure storage link exists
echo "ENSURING STORAGE LINK EXISTS:\n";
$storageLink = public_path('storage');
if (!file_exists($storageLink) || !is_link($storageLink)) {
    if (file_exists($storageLink)) {
        if (is_dir($storageLink)) {
            File::deleteDirectory($storageLink);
        } else {
            unlink($storageLink);
        }
    }
    symlink(storage_path('app/public'), $storageLink);
    echo "Storage link created/fixed.\n";
} else {
    echo "Storage link already exists.\n";
}

// 2. Ensure directories exist
echo "\nENSURING DIRECTORIES EXIST:\n";
$directories = [
    public_path('storage/products'),
    public_path('storage/product-colors'),
    public_path('images/products'),
    public_path('images/product-colors'),
    storage_path('app/public/products'),
    storage_path('app/public/product-colors'),
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// 3. Create a sample image
echo "\nCREATING SAMPLE IMAGES:\n";
$sampleImagePaths = [
    'red' => 'sample_red.jpg',
    'blue' => 'sample_blue.jpg',
    'green' => 'sample_green.jpg',
    'black' => 'sample_black.jpg',
    'white' => 'sample_white.jpg',
];

foreach ($sampleImagePaths as $color => $filename) {
    // Create a simple image
    $width = 400;
    $height = 300;
    $image = imagecreatetruecolor($width, $height);
    
    // Set color
    switch ($color) {
        case 'red':
            $bgColor = imagecolorallocate($image, 220, 50, 50);
            break;
        case 'blue':
            $bgColor = imagecolorallocate($image, 50, 50, 220);
            break;
        case 'green':
            $bgColor = imagecolorallocate($image, 50, 220, 50);
            break;
        case 'black':
            $bgColor = imagecolorallocate($image, 30, 30, 30);
            break;
        case 'white':
            $bgColor = imagecolorallocate($image, 240, 240, 240);
            break;
    }
    
    $textColor = ($color == 'white' || $color == 'green') ? 
        imagecolorallocate($image, 30, 30, 30) : 
        imagecolorallocate($image, 255, 255, 255);
    
    // Fill background
    imagefill($image, 0, 0, $bgColor);
    
    // Add text
    $text = "Sample $color Product";
    $font = 5; // Built-in font
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    imagestring($image, $font, $x, $y, $text, $textColor);
    
    // Save the image to all necessary locations
    $locations = [
        public_path("storage/products/$filename"),
        public_path("storage/product-colors/$filename"),
        storage_path("app/public/products/$filename"),
        storage_path("app/public/product-colors/$filename"),
    ];
    
    foreach ($locations as $location) {
        imagejpeg($image, $location, 90);
    }
    
    imagedestroy($image);
    echo "Created $color sample image in all locations\n";
}

// 4. Fix product images
echo "\nFIXING PRODUCT IMAGES:\n";
$products = Product::with('colors')->get();
echo "Found " . $products->count() . " products to fix.\n";

$fixedProducts = 0;
$fixedColors = 0;

foreach ($products as $product) {
    $needsSave = false;
    $imagePath = $product->getRawOriginal('image');
    
    // Check if image path is valid and file exists
    $imageExists = false;
    if (!empty($imagePath)) {
        $filename = basename($imagePath);
        $possiblePaths = [
            public_path("storage/products/$filename"),
            public_path("storage/product-colors/$filename"),
            public_path(ltrim($imagePath, '/')),
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $imageExists = true;
                break;
            }
        }
    }
    
    // If image doesn't exist, assign a sample image
    if (!$imageExists) {
        $sampleImage = '/storage/products/' . array_values($sampleImagePaths)[0];
        $product->image = $sampleImage;
        $needsSave = true;
    }
    
    // Fix color images
    $hasDefaultColor = false;
    foreach ($product->colors as $color) {
        $colorNeedsSave = false;
        $colorImagePath = $color->getRawOriginal('image');
        
        // Check if color image exists
        $colorImageExists = false;
        if (!empty($colorImagePath)) {
            $filename = basename($colorImagePath);
            $possiblePaths = [
                public_path("storage/product-colors/$filename"),
                public_path("storage/products/$filename"),
                public_path(ltrim($colorImagePath, '/')),
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $colorImageExists = true;
                    break;
                }
            }
        }
        
        // If color image doesn't exist, assign a sample image
        if (!$colorImageExists) {
            // Choose a sample image based on color name if possible
            $colorName = strtolower($color->name);
            $matchedColor = null;
            
            foreach (array_keys($sampleImagePaths) as $sampleColor) {
                if (strpos($colorName, $sampleColor) !== false) {
                    $matchedColor = $sampleColor;
                    break;
                }
            }
            
            if (!$matchedColor) {
                // If no match, use a random sample
                $matchedColor = array_keys($sampleImagePaths)[array_rand(array_keys($sampleImagePaths))];
            }
            
            $sampleImage = '/storage/product-colors/' . $sampleImagePaths[$matchedColor];
            $color->image = $sampleImage;
            $colorNeedsSave = true;
        }
        
        // Check if this is a default color
        if ($color->is_default) {
            $hasDefaultColor = true;
            
            // If product image is missing, use this color's image
            if (!$imageExists && $colorNeedsSave) {
                $product->image = $color->image;
                $needsSave = true;
            }
        }
        
        if ($colorNeedsSave) {
            $color->save();
            $fixedColors++;
        }
    }
    
    // If no default color, set the first one as default
    if (!$hasDefaultColor && $product->colors->isNotEmpty()) {
        $firstColor = $product->colors->first();
        $firstColor->is_default = true;
        $firstColor->save();
        
        // Use this color's image for the product
        $product->image = $firstColor->image;
        $needsSave = true;
        
        $fixedColors++;
    }
    
    if ($needsSave) {
        $product->save();
        $fixedProducts++;
    }
}

echo "Fixed $fixedProducts products and $fixedColors colors.\n";
echo "\nFIX COMPLETE\n";
