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
        Schema::table('business_types', function (Blueprint $table) {
            // Arabic language support column
            if (!Schema::hasColumn('business_types', 'name_arabic')) {
                $table->string('name_arabic')->nullable()->after('business_name')
                    ->comment('Arabic translation of the business type name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_types', function (Blueprint $table) {
            if (Schema::hasColumn('business_types', 'name_arabic')) {
                $table->dropColumn('name_arabic');
            }
        });
    }
};
