<?php
// Script to add missing columns to the products table

// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Adding missing columns to prodsucts table...\n";
    
    Schema::table('products', function (Blueprint $table) {
        // Add sku column if it doesn't exist
        if (!Schema::hasColumn('products', 'sku')) {
            $table->string('sku')->nullable();
            echo "Added sku column.\n";
        }
        
        // Add original_price column if it doesn't exist
        if (!Schema::hasColumn('products', 'original_price')) {
            $table->decimal('original_price', 10, 2)->nullable();
            echo "Added original_price column.\n";
        }
    });
    
    echo "Products table update completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
