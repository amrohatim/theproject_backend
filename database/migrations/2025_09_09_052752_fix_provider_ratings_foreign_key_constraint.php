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
        if (!Schema::hasTable('provider_ratings')) {
            return;
        }

        Schema::table('provider_ratings', function (Blueprint $table) {
            // Drop the existing incorrect foreign key constraint
            $table->dropForeign(['provider_id']);

            // Add the correct foreign key constraint to providers table
            $table->foreign('provider_id')
                  ->references('id')
                  ->on('providers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('provider_ratings')) {
            return;
        }

        Schema::table('provider_ratings', function (Blueprint $table) {
            // Drop the correct foreign key constraint
            $table->dropForeign(['provider_id']);

            // Restore the original (incorrect) foreign key constraint
            $table->foreign('provider_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
