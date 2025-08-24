<?php

/**
 * This script downloads mock images for the seeded data.
 * Run this script from the command line: php download_mock_images.php
 */

// Define image directories
$directories = [
    'public/images/users',
    'public/images/companies',
    'public/images/branches',
    'public/images/categories',
    'public/images/products',
    'public/images/services',
];

// Ensure directories exist
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// Function to download an image
function downloadImage($url, $path) {
    echo "Downloading $url to $path... ";
    $content = file_get_contents($url);
    if ($content === false) {
        echo "FAILED\n";
        return false;
    }
    
    if (file_put_contents($path, $content) === false) {
        echo "FAILED to save\n";
        return false;
    }
    
    echo "SUCCESS\n";
    return true;
}

// User images
$userImages = [
    'admin.jpg' => 'https://randomuser.me/api/portraits/men/1.jpg',
    'vendor1.jpg' => 'https://randomuser.me/api/portraits/men/2.jpg',
    'vendor2.jpg' => 'https://randomuser.me/api/portraits/women/3.jpg',
    'vendor3.jpg' => 'https://randomuser.me/api/portraits/men/4.jpg',
    'customer1.jpg' => 'https://randomuser.me/api/portraits/women/5.jpg',
    'customer2.jpg' => 'https://randomuser.me/api/portraits/men/6.jpg',
    'customer3.jpg' => 'https://randomuser.me/api/portraits/women/7.jpg',
    'customer4.jpg' => 'https://randomuser.me/api/portraits/men/8.jpg',
];

// Company logos
$companyImages = [
    'tech-solutions.jpg' => 'https://loremflickr.com/320/320/technology,logo/all',
    'wellness-center.jpg' => 'https://loremflickr.com/320/320/wellness,logo/all',
    'gourmet-delights.jpg' => 'https://loremflickr.com/320/320/food,logo/all',
];

// Branch images
$branchImages = [
    'tech-downtown.jpg' => 'https://loremflickr.com/640/480/office,technology/all',
    'tech-valley.jpg' => 'https://loremflickr.com/640/480/silicon,valley/all',
    'wellness-beverly.jpg' => 'https://loremflickr.com/640/480/spa,wellness/all',
    'wellness-santa-monica.jpg' => 'https://loremflickr.com/640/480/beach,spa/all',
    'gourmet-manhattan.jpg' => 'https://loremflickr.com/640/480/restaurant,gourmet/all',
    'gourmet-brooklyn.jpg' => 'https://loremflickr.com/640/480/artisan,food/all',
];

// Category images
$categoryImages = [
    // Product categories
    'electronics.jpg' => 'https://loremflickr.com/640/480/electronics/all',
    'home-kitchen.jpg' => 'https://loremflickr.com/640/480/kitchen/all',
    'food-beverages.jpg' => 'https://loremflickr.com/640/480/food/all',
    'smartphones.jpg' => 'https://loremflickr.com/640/480/smartphone/all',
    'laptops.jpg' => 'https://loremflickr.com/640/480/laptop/all',
    'audio.jpg' => 'https://loremflickr.com/640/480/headphones/all',
    'furniture.jpg' => 'https://loremflickr.com/640/480/furniture/all',
    'appliances.jpg' => 'https://loremflickr.com/640/480/appliance/all',
    'snacks.jpg' => 'https://loremflickr.com/640/480/snack/all',
    'beverages.jpg' => 'https://loremflickr.com/640/480/drink/all',
    
    // Service categories
    'health-wellness.jpg' => 'https://loremflickr.com/640/480/wellness/all',
    'professional.jpg' => 'https://loremflickr.com/640/480/business/all',
    'home-services.jpg' => 'https://loremflickr.com/640/480/repair/all',
    'massage.jpg' => 'https://loremflickr.com/640/480/massage/all',
    'fitness.jpg' => 'https://loremflickr.com/640/480/fitness/all',
    'consulting.jpg' => 'https://loremflickr.com/640/480/consulting/all',
    'legal.jpg' => 'https://loremflickr.com/640/480/legal/all',
    'cleaning.jpg' => 'https://loremflickr.com/640/480/cleaning/all',
    'repairs.jpg' => 'https://loremflickr.com/640/480/repair/all',
];

// Product images
$productImages = [
    'smartphone-x.jpg' => 'https://loremflickr.com/640/480/smartphone,premium/all',
    'smartphone-y.jpg' => 'https://loremflickr.com/640/480/smartphone,budget/all',
    'ultrabook-pro.jpg' => 'https://loremflickr.com/640/480/laptop,ultrabook/all',
    'gaming-laptop.jpg' => 'https://loremflickr.com/640/480/laptop,gaming/all',
    'headphones.jpg' => 'https://loremflickr.com/640/480/headphones,wireless/all',
    'bluetooth-speaker.jpg' => 'https://loremflickr.com/640/480/speaker,bluetooth/all',
    'office-chair.jpg' => 'https://loremflickr.com/640/480/chair,office/all',
    'coffee-table.jpg' => 'https://loremflickr.com/640/480/table,coffee/all',
    'blender.jpg' => 'https://loremflickr.com/640/480/blender/all',
    'air-fryer.jpg' => 'https://loremflickr.com/640/480/airfryer/all',
    'mixed-nuts.jpg' => 'https://loremflickr.com/640/480/nuts,mixed/all',
    'potato-chips.jpg' => 'https://loremflickr.com/640/480/chips,potato/all',
    'coffee-beans.jpg' => 'https://loremflickr.com/640/480/coffee,beans/all',
    'herbal-tea.jpg' => 'https://loremflickr.com/640/480/tea,herbal/all',
];

// Service images
$serviceImages = [
    'swedish-massage.jpg' => 'https://loremflickr.com/640/480/massage,swedish/all',
    'deep-tissue.jpg' => 'https://loremflickr.com/640/480/massage,deep/all',
    'personal-training.jpg' => 'https://loremflickr.com/640/480/fitness,training/all',
    'yoga.jpg' => 'https://loremflickr.com/640/480/yoga/all',
    'business-consulting.jpg' => 'https://loremflickr.com/640/480/business,consulting/all',
    'financial-planning.jpg' => 'https://loremflickr.com/640/480/financial,planning/all',
    'legal-consultation.jpg' => 'https://loremflickr.com/640/480/legal,consultation/all',
    'document-review.jpg' => 'https://loremflickr.com/640/480/document,review/all',
    'home-cleaning.jpg' => 'https://loremflickr.com/640/480/cleaning,home/all',
    'deep-cleaning.jpg' => 'https://loremflickr.com/640/480/cleaning,deep/all',
    'handyman.jpg' => 'https://loremflickr.com/640/480/handyman/all',
    'plumbing.jpg' => 'https://loremflickr.com/640/480/plumbing/all',
];

// Download all images
echo "Downloading user images...\n";
foreach ($userImages as $filename => $url) {
    downloadImage($url, "public/images/users/$filename");
}

echo "\nDownloading company images...\n";
foreach ($companyImages as $filename => $url) {
    downloadImage($url, "public/images/companies/$filename");
}

echo "\nDownloading branch images...\n";
foreach ($branchImages as $filename => $url) {
    downloadImage($url, "public/images/branches/$filename");
}

echo "\nDownloading category images...\n";
foreach ($categoryImages as $filename => $url) {
    downloadImage($url, "public/images/categories/$filename");
}

echo "\nDownloading product images...\n";
foreach ($productImages as $filename => $url) {
    downloadImage($url, "public/images/products/$filename");
}

echo "\nDownloading service images...\n";
foreach ($serviceImages as $filename => $url) {
    downloadImage($url, "public/images/services/$filename");
}

echo "\nAll images downloaded successfully!\n";
