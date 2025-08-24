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
            // Drop the foreign key constraint first
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {
                // Ignore if the constraint doesn't exist
            }

            // Change the product_id column to be nullable
            $table->unsignedBigInteger('product_id')->nullable()->change();

            // Add the foreign key constraint back with nullOnDelete
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();

            // We'll skip dropping the unique constraint as it's needed for foreign key constraints
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_products', function (Blueprint $table) {
            // Drop the foreign key constraint first
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {
                // Ignore if the constraint doesn't exist
            }

            // Change the product_id column back to non-nullable
            $table->unsignedBigInteger('product_id')->nullable(false)->change();

            // Add the foreign key constraint back with onDelete('cascade')
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Add the unique constraint back
            $table->unique(['provider_id', 'product_id']);
        });
    }
};
