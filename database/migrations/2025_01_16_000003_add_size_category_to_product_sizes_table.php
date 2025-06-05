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
            // Add size category reference
            $table->foreignId('size_category_id')->nullable()->after('product_id')->constrained()->onDelete('set null');
            
            // Add reference to standardized size (optional, for validation)
            $table->foreignId('standardized_size_id')->nullable()->after('size_category_id')->constrained()->onDelete('set null');
            
            // Add index for better performance
            $table->index(['product_id', 'size_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropForeign(['size_category_id']);
            $table->dropForeign(['standardized_size_id']);
            $table->dropIndex(['product_id', 'size_category_id']);
            $table->dropColumn(['size_category_id', 'standardized_size_id']);
        });
    }
};
