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
        Schema::table('categories', function (Blueprint $table) {
            // Add size category mapping to product categories
            $table->foreignId('default_size_category_id')->nullable()->after('type')->constrained('size_categories')->onDelete('set null');
            
            // Add index for better performance
            $table->index(['type', 'default_size_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['default_size_category_id']);
            $table->dropIndex(['type', 'default_size_category_id']);
            $table->dropColumn('default_size_category_id');
        });
    }
};
