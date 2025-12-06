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
        Schema::table('size_categories', function (Blueprint $table) {
            $table->string('name_arabic')->nullable()->after('name');
            $table->string('display_name_arabic')->nullable()->after('display_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('size_categories', function (Blueprint $table) {
            $table->dropColumn(['name_arabic', 'display_name_arabic']);
        });
    }
};
