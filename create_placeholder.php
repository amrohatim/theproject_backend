<?php

// This script creates a placeholder image for products

// Define paths
$publicPath = __DIR__ . '/public';
$imagesPath = $publicPath . '/images';
$placeholderPath = $imagesPath . '/placeholder.jpg';

echo "Creating placeholder image...\n";

// Ensure the images directory exists
if (!file_exists($imagesPath)) {
    echo "Creating images directory...\n";
    mkdir($imagesPath, 0755, true);
    echo "Images directory created.\n";
}

// Create a simple placeholder image using GD
$width = 300;
$height = 300;
$image = imagecreatetruecolor($width, $height);

// Set background color (light gray)
$bgColor = imagecolorallocate($image, 240, 240, 240);
imagefill($image, 0, 0, $bgColor);

// Add a border
$borderColor = imagecolorallocate($image, 200, 200, 200);
imagerectangle($image, 0, 0, $width - 1, $height - 1, $borderColor);

// Add a camera icon (simplified)
$iconColor = imagecolorallocate($image, 150, 150, 150);
$centerX = $width / 2;
$centerY = $height / 2;
$size = min($width, $height) / 4;

// Draw camera body
imagefilledrectangle($image, $centerX - $size, $centerY - $size/2, $centerX + $size, $centerY + $size, $iconColor);

// Draw camera lens
imagefilledellipse($image, $centerX, $centerY, $size, $size, $borderColor);
imagefilledellipse($image, $centerX, $centerY, $size * 0.7, $size * 0.7, $bgColor);

// Add text
$textColor = imagecolorallocate($image, 100, 100, 100);
$text = "No Image";
$font = 5; // Built-in font
$textWidth = imagefontwidth($font) * strlen($text);
$textHeight = imagefontheight($font);
$textX = $centerX - ($textWidth / 2);
$textY = $centerY + $size + 20;
imagestring($image, $font, $textX, $textY, $text, $textColor);

// Save the image
imagejpeg($image, $placeholderPath, 90);
imagedestroy($image);

echo "Placeholder image created at: $placeholderPath\n";

// Also create a copy in the storage directory
$storagePath = __DIR__ . '/storage/app/public';
$storageProductsPath = $storagePath . '/products';
$storageImagesPath = $storagePath . '/images';

// Ensure the storage directories exist
if (!file_exists($storagePath)) {
    echo "Creating storage/app/public directory...\n";
    mkdir($storagePath, 0755, true);
    echo "Storage directory created.\n";
}

if (!file_exists($storageProductsPath)) {
    echo "Creating storage/app/public/products directory...\n";
    mkdir($storageProductsPath, 0755, true);
    echo "Products directory created.\n";
}

if (!file_exists($storageImagesPath)) {
    echo "Creating storage/app/public/images directory...\n";
    mkdir($storageImagesPath, 0755, true);
    echo "Images directory created.\n";
}

// Copy the placeholder to storage locations
copy($placeholderPath, $storagePath . '/placeholder.jpg');
copy($placeholderPath, $storageProductsPath . '/placeholder.jpg');
copy($placeholderPath, $storageImagesPath . '/placeholder.jpg');

echo "Placeholder image copied to storage locations.\n";
echo "Done.\n";
