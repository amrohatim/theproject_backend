<?php

// Define paths
$publicPath = __DIR__ . '/public';
$storagePath = __DIR__ . '/storage/app/public';
$linkPath = $publicPath . '/storage';

echo "Checking storage link...\n";
echo "Public path: $publicPath\n";
echo "Storage path: $storagePath\n";
echo "Link path: $linkPath\n";

// Check if the storage directory exists
if (!file_exists($storagePath)) {
    echo "Creating storage directory...\n";
    mkdir($storagePath, 0755, true);
    echo "Storage directory created.\n";
}

// Check if the link already exists
if (file_exists($linkPath)) {
    echo "Link already exists.\n";
    
    // Check if it's a symbolic link
    if (is_link($linkPath)) {
        echo "It's a symbolic link pointing to: " . readlink($linkPath) . "\n";
    } else {
        echo "It's not a symbolic link. Removing it...\n";
        
        // If it's a directory, remove it recursively
        if (is_dir($linkPath)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($linkPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($files as $fileinfo) {
                $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $action($fileinfo->getRealPath());
            }
            
            rmdir($linkPath);
        } else {
            unlink($linkPath);
        }
        
        echo "Removed. Creating symbolic link...\n";
        symlink($storagePath, $linkPath);
        echo "Symbolic link created.\n";
    }
} else {
    echo "Link doesn't exist. Creating symbolic link...\n";
    symlink($storagePath, $linkPath);
    echo "Symbolic link created.\n";
}

// Check if the products directory exists in storage
$productsPath = $storagePath . '/products';
if (!file_exists($productsPath)) {
    echo "Creating products directory in storage...\n";
    mkdir($productsPath, 0755, true);
    echo "Products directory created.\n";
}

// Check if the product-colors directory exists in storage
$productColorsPath = $storagePath . '/product-colors';
if (!file_exists($productColorsPath)) {
    echo "Creating product-colors directory in storage...\n";
    mkdir($productColorsPath, 0755, true);
    echo "Product-colors directory created.\n";
}

echo "Done.\n";
