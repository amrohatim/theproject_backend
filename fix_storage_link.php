<?php

// This script ensures the storage symbolic link is properly set up

// Check if the storage directory exists in public
if (!file_exists(public_path('storage'))) {
    echo "Storage symbolic link does not exist. Creating it now...\n";
    
    // Create the symbolic link
    try {
        symlink(storage_path('app/public'), public_path('storage'));
        echo "Symbolic link created successfully.\n";
    } catch (Exception $e) {
        echo "Error creating symbolic link: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "Storage symbolic link already exists.\n";
    
    // Verify it's pointing to the correct location
    $target = readlink(public_path('storage'));
    $expected = storage_path('app/public');
    
    if ($target !== $expected) {
        echo "Warning: Symbolic link exists but points to incorrect location.\n";
        echo "Current target: $target\n";
        echo "Expected target: $expected\n";
        
        // Remove the existing link and create a new one
        echo "Removing incorrect link and creating a new one...\n";
        unlink(public_path('storage'));
        symlink($expected, public_path('storage'));
        echo "Symbolic link fixed.\n";
    } else {
        echo "Symbolic link is correctly configured.\n";
    }
}

// Create required directories if they don't exist
$directories = [
    storage_path('app/public/products'),
    storage_path('app/public/product-colors'),
    public_path('images/products'),
    public_path('images/product-colors')
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        echo "Creating directory: $dir\n";
        mkdir($dir, 0755, true);
    }
}

echo "Storage setup complete.\n";
