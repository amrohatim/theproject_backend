<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if deals table exists before trying to modify it
        if (!Schema::hasTable('deals')) {
            return;
        }

        // Update the applies_to enum to include 'products_and_services' option
        DB::statement("ALTER TABLE deals MODIFY COLUMN applies_to ENUM('all', 'products', 'categories', 'services', 'products_and_services') DEFAULT 'all'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if deals table exists before trying to modify it
        if (!Schema::hasTable('deals')) {
            return;
        }

        // First, migrate existing deals with 'products_and_services' to 'products'
        DB::table('deals')
            ->where('applies_to', 'products_and_services')
            ->update(['applies_to' => 'products']);

        // Update the applies_to enum to remove 'products_and_services' option
        DB::statement("ALTER TABLE deals MODIFY COLUMN applies_to ENUM('all', 'products', 'categories', 'services') DEFAULT 'all'");
    }
};
