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
        // Check if the deals table exists before trying to modify it
        if (Schema::hasTable('deals')) {
            Schema::table('deals', function (Blueprint $table) {
                // Add service_ids column if it doesn't exist
                if (!Schema::hasColumn('deals', 'service_ids')) {
                    $table->json('service_ids')->nullable()->after('category_ids');
                }
            });

            // Update the applies_to enum to include service options
            DB::statement("ALTER TABLE deals MODIFY COLUMN applies_to ENUM('all', 'products', 'categories', 'services', 'products_and_services') DEFAULT 'all'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('deals')) {
            Schema::table('deals', function (Blueprint $table) {
                if (Schema::hasColumn('deals', 'service_ids')) {
                    $table->dropColumn('service_ids');
                }
            });

            // Revert the applies_to enum to original values
            DB::statement("ALTER TABLE deals MODIFY COLUMN applies_to ENUM('all', 'products', 'categories') DEFAULT 'all'");
        }
    }
};
