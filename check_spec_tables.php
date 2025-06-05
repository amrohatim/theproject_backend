<?php

// Check if the product specification tables exist
try {
    // Use the Laravel database connection
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    // Get the database connection
    $db = $app->make('db');
    
    // Check if the tables exist
    $tables = [
        'product_specifications',
        'product_colors',
        'product_sizes',
        'product_branches'
    ];
    
    echo "Checking if tables exist:\n";
    
    foreach ($tables as $table) {
        if ($db->getSchemaBuilder()->hasTable($table)) {
            echo "- $table: EXISTS\n";
        } else {
            echo "- $table: DOES NOT EXIST\n";
        }
    }
    
    // Create the tables if they don't exist
    echo "\nCreating tables that don't exist...\n";
    
    if (!$db->getSchemaBuilder()->hasTable('product_specifications')) {
        $db->getSchemaBuilder()->create('product_specifications', function ($table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
        echo "- Created product_specifications table\n";
    }
    
    if (!$db->getSchemaBuilder()->hasTable('product_colors')) {
        $db->getSchemaBuilder()->create('product_colors', function ($table) {
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
    }
    
    if (!$db->getSchemaBuilder()->hasTable('product_sizes')) {
        $db->getSchemaBuilder()->create('product_sizes', function ($table) {
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
    }
    
    if (!$db->getSchemaBuilder()->hasTable('product_branches')) {
        $db->getSchemaBuilder()->create('product_branches', function ($table) {
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
    }
    
    // Add is_multi_branch column to products table if it doesn't exist
    if (!$db->getSchemaBuilder()->hasColumn('products', 'is_multi_branch')) {
        $db->getSchemaBuilder()->table('products', function ($table) {
            $table->boolean('is_multi_branch')->default(false)->after('is_available');
        });
        echo "- Added is_multi_branch column to products table\n";
    }
    
    echo "\nDone!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
