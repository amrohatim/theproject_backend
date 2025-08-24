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
        // Check if the table exists before trying to modify it
        if (Schema::hasTable('product_sizes')) {
            // Check if the column doesn't already exist
            if (!Schema::hasColumn('product_sizes', 'additional_info')) {
                Schema::table('product_sizes', function (Blueprint $table) {
                    $table->string('additional_info')->nullable()->after('value');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the table and column exist before trying to drop the column
        if (Schema::hasTable('product_sizes') && Schema::hasColumn('product_sizes', 'additional_info')) {
            Schema::table('product_sizes', function (Blueprint $table) {
                $table->dropColumn('additional_info');
            });
        }
    }
};
