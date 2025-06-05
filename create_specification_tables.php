<?php

// Script to manually create product specification tables
// Run with: php create_specification_tables.php

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

try {
    echo "Starting table creation process...\n";
    
    // Create product specifications table
    if (!Schema::hasTable('product_specifications')) {
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
        echo "- Created product_specifications table\n";
    } else {
        echo "- product_specifications table already exists\n";
    }
    
    // Create product colors table
    if (!Schema::hasTable('product_colors')) {
        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('color_code', 10)->nullable(); // Hex color code
            $table->string('image')->nullable(); // Image URL for this color
            $table->decimal('price_adjustment', 10, 2)->default(0); // Price adjustment for this color
            $table->integer('stock')->default(0); // Stock for this color
            $table->integer('display_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
        echo "- Created product_colors table\n";
    } else {
        echo "- product_colors table already exists\n";
    }
    
    // Create product sizes table
    if (!Schema::hasTable('product_sizes')) {
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., S, M, L, XL
            $table->string('value')->nullable(); // Actual value (e.g., dimensions)
            $table->decimal('price_adjustment', 10, 2)->default(0); // Price adjustment for this size
            $table->integer('stock')->default(0); // Stock for this size
            $table->integer('display_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
        echo "- Created product_sizes table\n";
    } else {
        echo "- product_sizes table already exists\n";
    }
    
    // Create product branches table
    if (!Schema::hasTable('product_branches')) {
        Schema::create('product_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->integer('stock')->default(0); // Stock for this product in this branch
            $table->boolean('is_available')->default(true);
            $table->decimal('price', 10, 2)->nullable(); // Optional branch-specific price
            $table->timestamps();

            // Ensure a product can't be associated with the same branch twice
            $table->unique(['product_id', 'branch_id']);
        });
        echo "- Created product_branches table\n";
    } else {
        echo "- product_branches table already exists\n";
    }
    
    // Add is_multi_branch column to products table if it doesn't exist
    if (!Schema::hasColumn('products', 'is_multi_branch')) {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_multi_branch')->default(false)->after('is_available');
        });
        echo "- Added is_multi_branch column to products table\n";
    } else {
        echo "- is_multi_branch column already exists in products table\n";
    }
    
    // Add migration records to the migrations table
    $migrations = [
        '2025_07_15_000001_create_product_specifications_table',
        '2025_07_15_000002_create_service_specifications_table',
        '2025_07_15_000003_create_product_branches_table',
        '2025_08_01_000001_create_product_specifications_tables'
    ];
    
    $maxBatch = DB::table('migrations')->max('batch') + 1;
    
    foreach ($migrations as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        
        if (!$exists) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $maxBatch
            ]);
            echo "- Added migration record for {$migration}\n";
        } else {
            echo "- Migration record for {$migration} already exists\n";
        }
    }
    
    echo "Table creation process completed successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
