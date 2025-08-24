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
        // Check if required tables exist before creating the relationship table
        if (!Schema::hasTable('product_colors') || !Schema::hasTable('product_sizes')) {
            // Required tables don't exist yet, skip this migration
            return;
        }

        // Create product color sizes relationship table only if it doesn't exist
        if (!Schema::hasTable('product_color_sizes')) {
            Schema::create('product_color_sizes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_color_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_size_id')->constrained()->onDelete('cascade');
                $table->integer('stock')->default(0); // Stock for this specific color-size combination
                $table->decimal('price_adjustment', 10, 2)->default(0); // Additional price adjustment for this combination
                $table->boolean('is_available')->default(true);
                $table->timestamps();

                // Ensure unique combinations
                $table->unique(['product_color_id', 'product_size_id'], 'unique_color_size_combination');

                // Add indexes for better performance
                $table->index(['product_id', 'product_color_id']);
                $table->index(['product_id', 'product_size_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_color_sizes');
    }
};
