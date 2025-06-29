<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
        }

        // Add a flag to the products table to indicate if it's a multi-branch product
        if (!Schema::hasColumn('products', 'is_multi_branch')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_multi_branch')->default(false)->after('is_available');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the tables in reverse order
        Schema::dropIfExists('product_branches');
        
        // Remove the is_multi_branch column from products table
        if (Schema::hasColumn('products', 'is_multi_branch')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('is_multi_branch');
            });
        }
        
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('product_colors');
        Schema::dropIfExists('product_specifications');
    }
};
