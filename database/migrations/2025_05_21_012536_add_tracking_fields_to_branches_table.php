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
        Schema::table('branches', function (Blueprint $table) {
            $table->integer('view_count')->default(0);
            $table->integer('order_count')->default(0);
            $table->integer('popularity_score')->default(0)->index();
            $table->timestamp('last_score_calculation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'view_count',
                'order_count',
                'popularity_score',
                'last_score_calculation'
            ]);
        });
    }
};
