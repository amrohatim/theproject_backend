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
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('view_count')->default(0);
            $table->integer('purchase_count')->default(0);
            $table->integer('trending_score')->default(0)->index();
            $table->timestamp('last_trending_calculation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'view_count',
                'purchase_count',
                'trending_score',
                'last_trending_calculation'
            ]);
        });
    }
};
