<?php
// Script to fix provider_products table structure

// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting provider_products table fix script...\n";

// Add product_name column if it doesn't exist
if (!Schema::hasColumn('provider_products', 'product_name')) {
    DB::statement('ALTER TABLE provider_products ADD COLUMN product_name VARCHAR(255) NULL AFTER product_id');
    echo "Added product_name column.\n";
}

// Add description column if it doesn't exist
if (!Schema::hasColumn('provider_products', 'description')) {
    DB::statement('ALTER TABLE provider_products ADD COLUMN description TEXT NULL AFTER product_name');
    echo "Added description column.\n";
}

// Add price column if it doesn't exist
if (!Schema::hasColumn('provider_products', 'price')) {
    DB::statement('ALTER TABLE provider_products ADD COLUMN price DECIMAL(10,2) NULL AFTER description');
    echo "Added price column.\n";
}

// Add original_price column if it doesn't exist
if (!Schema::hasColumn('provider_products', 'original_price')) {
    DB::statement('ALTER TABLE provider_products ADD COLUMN original_price DECIMAL(10,2) NULL AFTER price');
    echo "Added original_price column.\n";
}

// Add stock column if it doesn't exist
if (!Schema::hasColumn('provider_products', 'stock')) {
    DB::statement('ALTER TABLE provider_products ADD COLUMN stock INT DEFAULT 0 AFTER original_price');
    echo "Added stock column.\n";
}

// Add sku column if it doesn't exist
if (!Schema::hasColumn('provider_products', 'sku')) {
    DB::statement('ALTER TABLE provider_products ADD COLUMN sku VARCHAR(100) NULL AFTER stock');
    echo "Added sku column.\n";
}

// Add category_id column if it doesn't exist
if (!Schema::hasColumn('provider_products', 'category_id')) {
    DB::statement('ALTER TABLE provider_products ADD COLUMN category_id BIGINT UNSIGNED NULL AFTER sku');
    
    // Check if categories table exists before adding foreign key
    if (Schema::hasTable('categories')) {
        try {
            DB::statement('ALTER TABLE provider_products ADD CONSTRAINT provider_products_category_id_foreign FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL');
            echo "Added category_id column with foreign key.\n";
        } catch (\Exception $e) {
            echo "Added category_id column without foreign key: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Added category_id column without foreign key (categories table not found).\n";
    }
}

// Add is_active column if it doesn't exist
if (!Schema::hasColumn('provider_products', 'is_active')) {
    DB::statement('ALTER TABLE provider_products ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER category_id');
    echo "Added is_active column.\n";
}

// Add image column if it doesn't exist
if (!Schema::hasColumn('provider_products', 'image')) {
    DB::statement('ALTER TABLE provider_products ADD COLUMN image VARCHAR(255) NULL');
    echo "Added image column.\n";
}

echo "Provider_products table fix completed.\n";
echo "Current columns: " . implode(", ", Schema::getColumnListing('provider_products')) . "\n";
