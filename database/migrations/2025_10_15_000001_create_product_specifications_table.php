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
        // Create product specifications table if it doesn't exist
        if (!Schema::hasTable('product_specifications')) {
            Schema::create('product_specifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('key');
                $table->text('value');
                $table->integer('display_order')->default(0);
                $table->timestamps();
                
                // Add an index for faster lookups
                $table->index(['product_id', 'key']);
            });
            
            echo "Created product_specifications table.\n";
        } else {
            echo "product_specifications table already exists.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
