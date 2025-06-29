<?php

// This script creates a placeholder image for products

// Create directories if they don't exist
$directories = [
    __DIR__ . '/public/images',
    __DIR__ . '/public/storage/products',
    __DIR__ . '/public/storage/product-colors',
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// Create a simple placeholder image
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
$text = "Product Image";
$font = 5; // Built-in font
$textWidth = imagefontwidth($font) * strlen($text);
$textHeight = imagefontheight($font);
$x = ($width - $textWidth) / 2;
$y = ($height - $textHeight) / 2;
imagestring($image, $font, $x, $y, $text, $textColor);

// Save the image to multiple locations
$locations = [
    __DIR__ . '/public/images/placeholder.jpg',
    __DIR__ . '/public/storage/products/placeholder.jpg',
    __DIR__ . '/public/storage/product-colors/placeholder.jpg',
];

foreach ($locations as $location) {
    imagejpeg($image, $location, 90);
    echo "Created placeholder image at: $location\n";
}

imagedestroy($image);

echo "Placeholder images created successfully.\n";
