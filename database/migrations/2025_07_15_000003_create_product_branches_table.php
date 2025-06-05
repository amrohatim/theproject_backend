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
        Schema::create('product_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->integer('stock')->default(0); // Stock for this product in this branch
            $table->boolean('is_available')->default(true);
            $table->decimal('price', 10, 2)->nullable(); // Optional branch-specific price
            $table->timestamps();

            // Ensure a product can't be associated with the same branch twice
            $table->unique(['product_id', 'branch_id']);
        });

        // Add a flag to the products table to indicate if it's a multi-branch product
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_multi_branch')->default(false)->after('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_multi_branch');
        });
        
        Schema::dropIfExists('product_branches');
    }
};
