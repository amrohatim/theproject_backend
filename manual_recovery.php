<?php

// Simple manual recovery script
echo "Starting manual database recovery...\n";

// First, let's create categories manually using SQL
$categories = [
    // Parent categories
    ['name' => 'Clothes', 'description' => 'Women\'s clothing and apparel', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Footwear', 'description' => 'Shoes and footwear', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Accessories', 'description' => 'Fashion accessories', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Bags', 'description' => 'Handbags and purses', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Jewelry', 'description' => 'Fashion jewelry', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Makeup', 'description' => 'Cosmetics and beauty', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Skincare', 'description' => 'Skincare products', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Haircare', 'description' => 'Hair care products', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Fragrances', 'description' => 'Perfumes and fragrances', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Intimates', 'description' => 'Undergarments', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Baby Clothing', 'description' => 'Baby clothes', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Baby Gear', 'description' => 'Baby equipment', 'type' => 'product', 'parent_id' => null],
    ['name' => 'Watches', 'description' => 'Timepieces', 'type' => 'product', 'parent_id' => null],
];

// Generate SQL for categories
echo "-- SQL to create missing categories:\n";
foreach ($categories as $cat) {
    $name = addslashes($cat['name']);
    $desc = addslashes($cat['description']);
    $type = $cat['type'];
    $parent = $cat['parent_id'] ? $cat['parent_id'] : 'NULL';
    
    echo "INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, created_at, updated_at) VALUES ('{$name}', '{$desc}', '{$type}', {$parent}, 1, NOW(), NOW());\n";
}

echo "\n-- SQL to create subcategories (run after parent categories):\n";

$subcategories = [
    ['name' => 'Activewear', 'parent' => 'Clothes'],
    ['name' => 'Dresses', 'parent' => 'Clothes'],
    ['name' => 'Loungewear', 'parent' => 'Clothes'],
    ['name' => 'Outerwear (jackets, coats)', 'parent' => 'Clothes'],
    ['name' => 'Tops (blouses, tunics)', 'parent' => 'Clothes'],
    ['name' => 'Bottoms (jeans, skirts)', 'parent' => 'Clothes'],
    
    ['name' => 'Sneakers', 'parent' => 'Footwear'],
    ['name' => 'Boots', 'parent' => 'Footwear'],
    ['name' => 'Heels', 'parent' => 'Footwear'],
    ['name' => 'Flats', 'parent' => 'Footwear'],
    ['name' => 'Sandals', 'parent' => 'Footwear'],
    
    ['name' => 'Belts', 'parent' => 'Accessories'],
    ['name' => 'Hats', 'parent' => 'Accessories'],
    ['name' => 'Scarves', 'parent' => 'Accessories'],
    ['name' => 'Sunglasses', 'parent' => 'Accessories'],
    
    ['name' => 'Backpacks', 'parent' => 'Bags'],
    ['name' => 'Crossbody bags', 'parent' => 'Bags'],
    ['name' => 'Tote bags', 'parent' => 'Bags'],
    
    ['name' => 'Earrings', 'parent' => 'Jewelry'],
    ['name' => 'Necklaces', 'parent' => 'Jewelry'],
    ['name' => 'Bracelets', 'parent' => 'Jewelry'],
    ['name' => 'Rings', 'parent' => 'Jewelry'],
    
    ['name' => 'Foundations', 'parent' => 'Makeup'],
    ['name' => 'Lipsticks', 'parent' => 'Makeup'],
    ['name' => 'Eyeshadows', 'parent' => 'Makeup'],
    ['name' => 'Mascaras', 'parent' => 'Makeup'],
    
    ['name' => 'Moisturizers', 'parent' => 'Skincare'],
    ['name' => 'Cleansers', 'parent' => 'Skincare'],
    ['name' => 'Serums', 'parent' => 'Skincare'],
    
    ['name' => 'Shampoos', 'parent' => 'Haircare'],
    ['name' => 'Conditioners', 'parent' => 'Haircare'],
    ['name' => 'Hair oils', 'parent' => 'Haircare'],
    
    ['name' => 'Perfumes', 'parent' => 'Fragrances'],
    ['name' => 'Body mists', 'parent' => 'Fragrances'],
    ['name' => 'Deodorants', 'parent' => 'Fragrances'],
    
    ['name' => 'Bras', 'parent' => 'Intimates'],
    ['name' => 'Panties', 'parent' => 'Intimates'],
    ['name' => 'Lingerie', 'parent' => 'Intimates'],
    
    ['name' => 'Onesies', 'parent' => 'Baby Clothing'],
    ['name' => 'Sleepwear', 'parent' => 'Baby Clothing'],
    
    ['name' => 'Strollers', 'parent' => 'Baby Gear'],
    ['name' => 'Car seats', 'parent' => 'Baby Gear'],
    ['name' => 'Baby carriers', 'parent' => 'Baby Gear'],
    
    ['name' => 'Analog', 'parent' => 'Watches'],
    ['name' => 'Digital', 'parent' => 'Watches'],
    ['name' => 'Smartwatches', 'parent' => 'Watches'],
];

foreach ($subcategories as $sub) {
    $name = addslashes($sub['name']);
    $parent = addslashes($sub['parent']);
    
    echo "INSERT IGNORE INTO categories (name, description, type, parent_id, is_active, created_at, updated_at) \n";
    echo "SELECT '{$name}', '{$name}', 'product', id, 1, NOW(), NOW() \n";
    echo "FROM categories WHERE name = '{$parent}' AND type = 'product' AND parent_id IS NULL;\n\n";
}

echo "\n-- Count products by checking image files:\n";
$productImagesPath = 'Products images';
if (is_dir($productImagesPath)) {
    $files = glob($productImagesPath . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    echo "Found " . count($files) . " product image files\n";
    
    echo "\nSample product images:\n";
    for ($i = 0; $i < min(10, count($files)); $i++) {
        echo "- " . basename($files[$i]) . "\n";
    }
}

echo "\nManual recovery script completed. Copy the SQL statements above and run them in your database.\n";
