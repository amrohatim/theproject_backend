<?php

// Path to the source placeholder image
$sourcePath = __DIR__ . '/storage/app/public/products/placeholder.png';

// Path to the destination with the required filename
$destinationPath = __DIR__ . '/storage/app/public/products/XBznwDfTU0I0pZRB4vZk24GsT9qhT3xUGoTjFKsh.png';

// Check if source file exists
if (!file_exists($sourcePath)) {
    echo "Error: Source placeholder image not found at {$sourcePath}\n";
    exit(1);
}

// Copy the file
if (copy($sourcePath, $destinationPath)) {
    echo "Success: Created image file at {$destinationPath}\n";
} else {
    echo "Error: Failed to copy the image file\n";
    exit(1);
}

// Verify the symbolic link exists
$publicStoragePath = __DIR__ . '/public/storage';
$appPublicPath = __DIR__ . '/storage/app/public';

if (!is_link($publicStoragePath)) {
    echo "Warning: The symbolic link does not exist. Running storage:link command...\n";
    
    // Execute the artisan command to create the link
    exec('php artisan storage:link', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "Success: Created symbolic link\n";
        echo implode("\n", $output) . "\n";
    } else {
        echo "Error: Failed to create symbolic link\n";
        echo implode("\n", $output) . "\n";
    }
} else {
    echo "Info: Symbolic link already exists\n";
}

echo "\nThe image should now be accessible at: http://192.168.70.48:8000/storage/products/XBznwDfTU0I0pZRB4vZk24GsT9qhT3xUGoTjFKsh.png\n";