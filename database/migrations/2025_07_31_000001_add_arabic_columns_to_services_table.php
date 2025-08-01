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
            // Arabic language support columns
            if (!Schema::hasColumn('services', 'service_name_arabic')) {
                $table->text('service_name_arabic')->nullable()->after('name')
                    ->comment('Arabic translation of the service name');
            }

            if (!Schema::hasColumn('services', 'service_description_arabic')) {
                $table->text('service_description_arabic')->nullable()->after('service_name_arabic')
                    ->comment('Arabic translation of the service description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Remove Arabic columns
            if (Schema::hasColumn('services', 'service_description_arabic')) {
                $table->dropColumn('service_description_arabic');
            }
            
            if (Schema::hasColumn('services', 'service_name_arabic')) {
                $table->dropColumn('service_name_arabic');
            }
        });
    }
};
