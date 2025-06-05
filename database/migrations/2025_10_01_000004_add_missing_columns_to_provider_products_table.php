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
        Schema::table('provider_products', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('provider_products', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }
            
            if (!Schema::hasColumn('provider_products', 'description')) {
                $table->text('description')->nullable()->after('product_name');
            }
            
            if (!Schema::hasColumn('provider_products', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('provider_products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('price');
            }
            
            if (!Schema::hasColumn('provider_products', 'stock')) {
                $table->integer('stock')->default(0)->after('original_price');
            }
            
            if (!Schema::hasColumn('provider_products', 'sku')) {
                $table->string('sku')->nullable()->after('stock');
            }
            
            if (!Schema::hasColumn('provider_products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('sku');
                // Add foreign key constraint
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('provider_products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('category_id');
            }
            
            if (!Schema::hasColumn('provider_products', 'image')) {
                $table->string('image')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_products', function (Blueprint $table) {
            // Remove columns in reverse order
            $columns = [
                'image', 'is_active', 'category_id', 'sku', 'stock', 
                'original_price', 'price', 'description', 'product_name'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('provider_products', $column)) {
                    if ($column === 'category_id') {
                        $table->dropForeign(['category_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
