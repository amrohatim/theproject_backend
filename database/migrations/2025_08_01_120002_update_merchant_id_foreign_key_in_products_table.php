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
        Schema::table('products', function (Blueprint $table) {
            // Drop the existing foreign key constraint if it exists
            $table->dropForeign(['merchant_id']);
            
            // Add the correct foreign key constraint to reference merchants table
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the merchants foreign key
            $table->dropForeign(['merchant_id']);
            
            // Restore the original foreign key to users table
            $table->foreign('merchant_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
