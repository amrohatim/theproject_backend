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
        Schema::table('provider_ratings', function (Blueprint $table) {
            // Drop the existing unique constraint and foreign key
            $table->dropUnique(['vendor_id', 'provider_id']);
            $table->dropForeign(['vendor_id']);

            // Rename vendor_id to user_id to support both vendors and merchants
            $table->renameColumn('vendor_id', 'user_id');
        });

        // Add the new constraints in a separate schema call (required for column rename)
        Schema::table('provider_ratings', function (Blueprint $table) {
            // Add foreign key constraint for user_id
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Add unique constraint to ensure one rating per user per provider
            $table->unique(['user_id', 'provider_id']);

            // Add index for performance
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_ratings', function (Blueprint $table) {
            // Drop the new constraints
            $table->dropUnique(['user_id', 'provider_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            // Rename user_id back to vendor_id
            $table->renameColumn('user_id', 'vendor_id');
        });

        // Restore the original constraints
        Schema::table('provider_ratings', function (Blueprint $table) {
            // Add back the original foreign key constraint
            $table->foreign('vendor_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Add back the original unique constraint
            $table->unique(['vendor_id', 'provider_id']);

            // Add back the original index
            $table->index(['vendor_id']);
        });
    }
};
