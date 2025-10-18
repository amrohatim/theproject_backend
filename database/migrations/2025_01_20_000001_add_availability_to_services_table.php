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
        Schema::table('services', function (Blueprint $table) {
            // Add availability columns
            $table->json('available_days')->nullable()->after('home_service')->comment('Array of available days (0=Sunday, 1=Monday, etc.)');
            $table->time('start_time')->nullable()->after('available_days')->comment('Service start time');
            $table->time('end_time')->nullable()->after('start_time')->comment('Service end time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['available_days', 'start_time', 'end_time']);
        });
    }
};