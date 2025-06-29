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
        // Check if the provider_products table exists and has the is_active column
        if (Schema::hasTable('provider_products') && Schema::hasColumn('provider_products', 'is_active')) {
            // Update all provider products to set is_active = true
            DB::table('provider_products')
                ->where('is_active', false)
                ->orWhereNull('is_active')
                ->update(['is_active' => true]);

            // Log the update
            $count = DB::table('provider_products')->count();
            $activeCount = DB::table('provider_products')->where('is_active', true)->count();

            echo "Updated provider_products table: {$activeCount} of {$count} products are now active.\n";
        } else {
            echo "provider_products table does not exist or is_active column is missing, skipping migration.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
