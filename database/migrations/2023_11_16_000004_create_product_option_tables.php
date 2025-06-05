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
        // Create product option types table if it doesn't exist
        if (!Schema::hasTable('product_option_types')) {
            Schema::create('product_option_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('display_name');
                $table->string('type')->default('select'); // select, text, checkbox, etc.
                $table->boolean('required')->default(false);
                $table->integer('display_order')->default(0);
                $table->timestamps();
            });
        }

        // Create product option values table if it doesn't exist
        if (!Schema::hasTable('product_option_values')) {
            Schema::create('product_option_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('option_type_id')->constrained('product_option_types')->onDelete('cascade');
                $table->string('value');
                $table->decimal('price_adjustment', 10, 2)->default(0);
                $table->integer('display_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_option_types');
    }
};
