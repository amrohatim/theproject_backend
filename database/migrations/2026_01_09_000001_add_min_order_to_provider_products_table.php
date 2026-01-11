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
        Schema::table('provider_products', function (Blueprint $table) {
            if (!Schema::hasColumn('provider_products', 'min_order')) {
                $table->unsignedInteger('min_order')->default(1)->after('stock');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_products', function (Blueprint $table) {
            if (Schema::hasColumn('provider_products', 'min_order')) {
                $table->dropColumn('min_order');
            }
        });
    }
};
