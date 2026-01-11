<?php
// Direct database update script for provider_products table

// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Updating provider_products table structure...\n";
    
    // Check if provider_products table exists
    if (!Schema::hasTable('provider_products')) {
        // Create the table if it doesn't exist
        Schema::create('provider_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('provider_profiles')->onDelete('cascade');
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->unsignedInteger('min_order')->default(1);
            $table->string('image')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->string('sku')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        echo "Created provider_products table from scratch.\n";
    } else {
        // If table exists, add missing columns
        Schema::table('provider_products', function (Blueprint $table) {
            if (!Schema::hasColumn('provider_products', 'provider_id')) {
                $table->foreignId('provider_id')->nullable()->constrained('provider_profiles')->nullOnDelete();
                echo "Added provider_id column.\n";
            }
            
            if (!Schema::hasColumn('provider_products', 'product_name')) {
                $table->string('product_name')->nullable();
                echo "Added product_name column.\n";
            }
            
            if (!Schema::hasColumn('provider_products', 'description')) {
                $table->text('description')->nullable();
                echo "Added description column.\n";
            }
            
            if (!Schema::hasColumn('provider_products', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
                echo "Added price column.\n";
            }
            
            if (!Schema::hasColumn('provider_products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable();
                echo "Added original_price column.\n";
            }
            
            if (!Schema::hasColumn('provider_products', 'stock')) {
                $table->integer('stock')->default(0);
                echo "Added stock column.\n";
            }

            if (!Schema::hasColumn('provider_products', 'min_order')) {
                $table->unsignedInteger('min_order')->default(1);
                echo "Added min_order column.\n";
            }
            
            if (!Schema::hasColumn('provider_products', 'image')) {
                $table->string('image')->nullable();
                echo "Added image column.\n";
            }
            
            if (!Schema::hasColumn('provider_products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable();
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
                echo "Added category_id column.\n";
            }
            
            if (!Schema::hasColumn('provider_products', 'sku')) {
                $table->string('sku')->nullable();
                echo "Added sku column.\n";
            }
            
            if (!Schema::hasColumn('provider_products', 'is_active')) {
                $table->boolean('is_active')->default(true);
                echo "Added is_active column.\n";
            }
        });
    }
    
    echo "Provider_products table structure update completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
