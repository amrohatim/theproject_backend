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
            // Drop all foreign key constraints first
            try {
                $table->dropForeign(['provider_id']);
            } catch (\Exception $e) {
                // Ignore if the constraint doesn't exist
            }

            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {
                // Ignore if the constraint doesn't exist
            }

            try {
                $table->dropForeign(['category_id']);
            } catch (\Exception $e) {
                // Ignore if the constraint doesn't exist
            }

            try {
                $table->dropForeign(['branch_id']);
            } catch (\Exception $e) {
                // Ignore if the constraint doesn't exist
            }
        });

        // Now drop the unique constraint in a separate schema operation
        Schema::table('provider_products', function (Blueprint $table) {
            try {
                $table->dropUnique(['provider_id', 'product_id']);
            } catch (\Exception $e) {
                // Ignore if the constraint doesn't exist
            }
        });

        // Now modify the column and recreate constraints
        Schema::table('provider_products', function (Blueprint $table) {
            // Change the product_id column to be nullable
            $table->unsignedBigInteger('product_id')->nullable()->change();

            // Recreate foreign key constraints
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();

            // Add branch_id column if it doesn't exist
            if (!Schema::hasColumn('provider_products', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('product_id');
                $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_products', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['product_id']);
            
            // Change the product_id column back to non-nullable
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            
            // Add the foreign key constraint back with onDelete('cascade')
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Add the unique constraint back
            $table->unique(['provider_id', 'product_id']);
            
            // Drop the branch_id column if it exists
            if (Schema::hasColumn('provider_products', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
    }
};
