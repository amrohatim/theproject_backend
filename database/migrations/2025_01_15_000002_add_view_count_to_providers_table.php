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
            $table->integer('view_count')->default(0)->after('total_ratings');
            $table->integer('order_count')->default(0)->after('view_count');
            $table->integer('provider_score')->default(0)->index()->after('order_count');
            $table->timestamp('last_score_calculation')->nullable()->after('provider_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn([
                'view_count',
                'order_count',
                'provider_score',
                'last_score_calculation'
            ]);
        });
    }
};
