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
        Schema::table('product_sizes', function (Blueprint $table) {
            // Add size_category_id column if it doesn't exist
            if (!Schema::hasColumn('product_sizes', 'size_category_id')) {
                $table->foreignId('size_category_id')->nullable()->after('product_id')->constrained('size_categories')->onDelete('set null');
            }

            // Add standardized_size_id column if it doesn't exist
            if (!Schema::hasColumn('product_sizes', 'standardized_size_id')) {
                $table->foreignId('standardized_size_id')->nullable()->after('size_category_id')->constrained('standardized_sizes')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            // Drop foreign keys and columns if they exist
            if (Schema::hasColumn('product_sizes', 'standardized_size_id')) {
                $table->dropForeign(['standardized_size_id']);
                $table->dropColumn('standardized_size_id');
            }

            if (Schema::hasColumn('product_sizes', 'size_category_id')) {
                $table->dropForeign(['size_category_id']);
                $table->dropColumn('size_category_id');
            }
        });
    }
};
