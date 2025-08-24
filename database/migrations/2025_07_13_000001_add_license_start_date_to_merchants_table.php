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
        Schema::table('merchants', function (Blueprint $table) {
            // Check if column exists before adding it
            if (!Schema::hasColumn('merchants', 'license_start_date')) {
                $table->date('license_start_date')->nullable()->after('license_file')->comment('License start date');
            }
        });

        // Add index for license_start_date
        try {
            Schema::table('merchants', function (Blueprint $table) {
                $table->index(['license_start_date']);
            });
        } catch (Exception $e) {
            // Index might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropIndex(['license_start_date']);
            $table->dropColumn('license_start_date');
        });
    }
};
