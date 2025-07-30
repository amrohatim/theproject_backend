<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds comprehensive database indexes to optimize
     * the products filtering and sorting functionality for large datasets.
     */
    public function up(): void
    {
        // Add indexes to products table for filtering and sorting performance
        Schema::table('products', function (Blueprint $table) {
            // Composite index for merchant products filtering (most common query)
            if (!$this->indexExists('products', 'products_user_filtering_idx')) {
                $table->index(['user_id', 'is_available', 'created_at'], 'products_user_filtering_idx');
            }
            
            // Index for price range filtering
            if (!$this->indexExists('products', 'products_price_idx')) {
                $table->index('price', 'products_price_idx');
            }
            
            // Index for stock level filtering
            if (!$this->indexExists('products', 'products_stock_idx')) {
                $table->index('stock', 'products_stock_idx');
            }
            
            // Index for name searching and sorting
            if (!$this->indexExists('products', 'products_name_idx')) {
                $table->index('name', 'products_name_idx');
            }
            
            // Index for SKU searching
            if (!$this->indexExists('products', 'products_sku_idx') && Schema::hasColumn('products', 'sku')) {
                $table->index('sku', 'products_sku_idx');
            }
            
            // Composite index for category-based filtering and sorting
            if (!$this->indexExists('products', 'products_category_sorting_idx')) {
                $table->index(['category_id', 'user_id', 'created_at'], 'products_category_sorting_idx');
            }
            
            // Composite index for status-based filtering
            if (!$this->indexExists('products', 'products_status_filtering_idx')) {
                $table->index(['user_id', 'is_available', 'stock'], 'products_status_filtering_idx');
            }
            
            // Index for date range filtering
            if (!$this->indexExists('products', 'products_created_at_idx')) {
                $table->index('created_at', 'products_created_at_idx');
            }
            
            // Composite index for multi-column sorting scenarios
            if (!$this->indexExists('products', 'products_multi_sort_idx')) {
                $table->index(['user_id', 'price', 'stock', 'created_at'], 'products_multi_sort_idx');
            }
        });

        // Add indexes to categories table for join performance
        Schema::table('categories', function (Blueprint $table) {
            // Index for active categories filtering (used in dropdowns)
            if (!$this->indexExists('categories', 'categories_active_idx')) {
                $table->index(['is_active', 'parent_id'], 'categories_active_idx');
            }
            
            // Index for category name sorting in joins
            if (!$this->indexExists('categories', 'categories_name_idx')) {
                $table->index('name', 'categories_name_idx');
            }
        });

        // Add indexes to users table for merchant filtering
        Schema::table('users', function (Blueprint $table) {
            // Index for role-based queries
            if (!$this->indexExists('users', 'users_role_status_idx')) {
                $table->index(['role', 'status'], 'users_role_status_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from products table
        Schema::table('products', function (Blueprint $table) {
            $indexes = [
                'products_user_filtering_idx',
                'products_price_idx',
                'products_stock_idx',
                'products_name_idx',
                'products_sku_idx',
                'products_category_sorting_idx',
                'products_status_filtering_idx',
                'products_created_at_idx',
                'products_multi_sort_idx'
            ];
            
            foreach ($indexes as $index) {
                if ($this->indexExists('products', $index)) {
                    $table->dropIndex($index);
                }
            }
        });

        // Remove indexes from categories table
        Schema::table('categories', function (Blueprint $table) {
            $indexes = [
                'categories_active_idx',
                'categories_name_idx'
            ];
            
            foreach ($indexes as $index) {
                if ($this->indexExists('categories', $index)) {
                    $table->dropIndex($index);
                }
            }
        });

        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            if ($this->indexExists('users', 'users_role_status_idx')) {
                $table->dropIndex('users_role_status_idx');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        try {
            $connection = Schema::getConnection();
            $indexes = $connection->select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};
