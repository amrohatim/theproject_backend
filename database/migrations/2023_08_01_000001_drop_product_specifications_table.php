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
        // Drop the product_specifications table if it exists
        Schema::dropIfExists('product_specifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the product_specifications table if needed
        if (!Schema::hasTable('product_specifications')) {
            Schema::create('product_specifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('key');
                $table->string('value');
                $table->integer('display_order')->default(0);
                $table->timestamps();
            });
        }
    }
};
