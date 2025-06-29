<?php
// Script to fix branch_id column in products table

// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Fixing branch_id column in products table...\n";
    
    // Option 1: Make branch_id nullable
    if (Schema::hasColumn('products', 'branch_id')) {
        DB::statement('ALTER TABLE products MODIFY branch_id BIGINT UNSIGNED NULL');
        echo "Made branch_id nullable.\n";
    }
    
    echo "Fix completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
