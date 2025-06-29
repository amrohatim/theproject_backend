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
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('color_code', 10)->nullable(); // Hex color code
            $table->string('image')->nullable(); // Image URL for this color
            $table->decimal('price_adjustment', 10, 2)->default(0); // Price adjustment for this color
            $table->integer('stock')->default(0); // Stock for this color
            $table->integer('display_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., S, M, L, XL
            $table->string('value')->nullable(); // Actual value (e.g., dimensions)
            $table->decimal('price_adjustment', 10, 2)->default(0); // Price adjustment for this size
            $table->integer('stock')->default(0); // Stock for this size
            $table->integer('display_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('product_colors');
        Schema::dropIfExists('product_specifications');
    }
};
