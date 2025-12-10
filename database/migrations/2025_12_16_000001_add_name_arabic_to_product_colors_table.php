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
        if (!Schema::hasColumn('product_colors', 'name_arabic')) {
            Schema::table('product_colors', function (Blueprint $table) {
                $table->string('name_arabic')->nullable()->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('product_colors', 'name_arabic')) {
            Schema::table('product_colors', function (Blueprint $table) {
                $table->dropColumn('name_arabic');
            });
        }
    }
}