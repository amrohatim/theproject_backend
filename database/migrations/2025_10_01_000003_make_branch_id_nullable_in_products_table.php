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
            // Drop the foreign key constraint first
            $table->dropForeign(['branch_id']);
            
            // Change the branch_id column to be nullable
            $table->unsignedBigInteger('branch_id')->nullable()->change();
            
            // Add the foreign key constraint back
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['branch_id']);
            
            // Change the branch_id column back to not nullable
            $table->unsignedBigInteger('branch_id')->nullable(false)->change();
            
            // Add the foreign key constraint back with cascade delete
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }
};
