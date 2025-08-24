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
        Schema::table('providers', function (Blueprint $table) {
            $table->boolean('delivery_capability')->default(false)->after('logo');
            $table->json('stock_locations')->nullable()->after('delivery_capability');
            $table->json('delivery_fees')->nullable()->after('stock_locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['delivery_capability', 'stock_locations', 'delivery_fees']);
        });
    }
};
