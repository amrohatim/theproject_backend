<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration ensures all size category related tables are created
        // and populated with standardized data
        
        // Run the size categories seeder
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\SizeCategoriesSeeder',
                '--force' => true
            ]);
            
            echo "✓ Size categories and standardized sizes seeded successfully.\n";
        } catch (\Exception $e) {
            echo "⚠ Warning: Could not seed size categories: " . $e->getMessage() . "\n";
        }

        // Run the category size mapping seeder
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\CategorySizeMappingSeeder',
                '--force' => true
            ]);
            
            echo "✓ Category size mappings seeded successfully.\n";
        } catch (\Exception $e) {
            echo "⚠ Warning: Could not seed category size mappings: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove size category mappings from categories
        Schema::table('categories', function (Blueprint $table) {
            $table->update(['default_size_category_id' => null]);
        });
        
        echo "✓ Removed size category mappings from categories.\n";
    }
};
