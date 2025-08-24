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
        Schema::create('standardized_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // XXS, XS, S, M, L, XL, etc. or 16, 17, 18, etc.
            $table->string('value')->nullable(); // Symbol representation or EU size value
            $table->string('additional_info')->nullable(); // Foot length, age group, etc.
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure unique size names within each category
            $table->unique(['size_category_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standardized_sizes');
    }
};
