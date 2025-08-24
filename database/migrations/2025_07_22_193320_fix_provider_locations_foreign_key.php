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
        Schema::table('provider_locations', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['provider_id']);
            
            // Add the new foreign key constraint referencing providers table
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_locations', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['provider_id']);
            
            // Restore the original foreign key constraint referencing provider_profiles table
            $table->foreign('provider_id')->references('id')->on('provider_profiles')->onDelete('cascade');
        });
    }
};
