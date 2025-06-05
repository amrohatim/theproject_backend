<?php
// Script to make product_id nullable or remove it

// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Fixing product_id column in provider_products table...\n";
    
    if (Schema::hasColumn('provider_products', 'product_id')) {
        // Option 1: Make product_id nullable
        DB::statement('ALTER TABLE provider_products MODIFY product_id BIGINT UNSIGNED NULL');
        echo "Made product_id nullable.\n";
        
        // Option 2: Drop foreign key if it exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = 'provider_products'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND CONSTRAINT_NAME LIKE '%product_id%'
        ");
        
        if (count($foreignKeys) > 0) {
            foreach ($foreignKeys as $key) {
                DB::statement("ALTER TABLE provider_products DROP FOREIGN KEY {$key->CONSTRAINT_NAME}");
                echo "Dropped foreign key {$key->CONSTRAINT_NAME}.\n";
            }
        }
    } else {
        echo "No product_id column found in the table.\n";
    }
    
    echo "Fix completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
