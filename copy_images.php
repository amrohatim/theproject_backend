<?php

// Create the public/images/products directory if it doesn't exist
if (!is_dir('public/images/products')) {
    echo "Creating public/images/products directory...\n";
    mkdir('public/images/products', 0755, true);
    echo "Directory created\n";
}

// Create the public/images/services directory if it doesn't exist
if (!is_dir('public/images/services')) {
    echo "Creating public/images/services directory...\n";
    mkdir('public/images/services', 0755, true);
    echo "Directory created\n";
}

// Create the public/images/deals directory if it doesn't exist
if (!is_dir('public/images/deals')) {
    echo "Creating public/images/deals directory...\n";
    mkdir('public/images/deals', 0755, true);
    echo "Directory created\n";
}

// Create the public/images/companies directory if it doesn't exist
if (!is_dir('public/images/companies')) {
    echo "Creating public/images/companies directory...\n";
    mkdir('public/images/companies', 0755, true);
    echo "Directory created\n";
}

// Copy all images from storage/app/public/products to public/images/products
echo "\nCopying product images from storage to public...\n";
if (is_dir('storage/app/public/products')) {
    $files = scandir('storage/app/public/products');
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $source = 'storage/app/public/products/' . $file;
            $destination = 'public/images/products/' . $file;
            if (copy($source, $destination)) {
                echo "Copied product image: " . $file . "\n";
            } else {
                echo "Failed to copy product image: " . $file . "\n";
            }
        }
    }
} else {
    echo "Source product directory does not exist\n";
}

// Copy all images from storage/app/public/services to public/images/services
echo "\nCopying service images from storage to public...\n";
if (is_dir('storage/app/public/services')) {
    $files = scandir('storage/app/public/services');
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $source = 'storage/app/public/services/' . $file;
            $destination = 'public/images/services/' . $file;
            if (copy($source, $destination)) {
                echo "Copied service image: " . $file . "\n";
            } else {
                echo "Failed to copy service image: " . $file . "\n";
            }
        }
    }
} else {
    echo "Source services directory does not exist\n";
}

// Copy all images from storage/app/public/deals to public/images/deals
echo "\nCopying deal images from storage to public...\n";
if (is_dir('storage/app/public/deals')) {
    $files = scandir('storage/app/public/deals');
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $source = 'storage/app/public/deals/' . $file;
            $destination = 'public/images/deals/' . $file;
            if (copy($source, $destination)) {
                echo "Copied deal image: " . $file . "\n";
            } else {
                echo "Failed to copy deal image: " . $file . "\n";
            }
        }
    }
} else {
    echo "Source deals directory does not exist\n";
}

// Copy all images from storage/app/public/companies to public/images/companies
echo "\nCopying company images from storage to public...\n";
if (is_dir('storage/app/public/companies')) {
    $files = scandir('storage/app/public/companies');
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $source = 'storage/app/public/companies/' . $file;
            $destination = 'public/images/companies/' . $file;
            if (copy($source, $destination)) {
                echo "Copied company image: " . $file . "\n";
            } else {
                echo "Failed to copy company image: " . $file . "\n";
            }
        }
    }
} else {
    echo "Source companies directory does not exist\n";
}

echo "\nImage copying complete\n";
