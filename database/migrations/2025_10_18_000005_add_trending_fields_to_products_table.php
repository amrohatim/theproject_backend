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
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'view_count')) {
                $table->integer('view_count')->default(0)->after('rating');
            }

            if (!Schema::hasColumn('products', 'order_count')) {
                $table->integer('order_count')->default(0)->after('view_count');
            }

            if (!Schema::hasColumn('products', 'trending_score')) {
                $table->integer('trending_score')->default(0)->after('order_count');
            }

            if (!Schema::hasColumn('products', 'last_trending_calculation')) {
                $table
                    ->timestamp('last_trending_calculation')
                    ->nullable()
                    ->after('trending_score');
            }

            // Helpful indexes for sorting/filtering by trending signals
            if (!Schema::hasColumn('products', 'view_count') || !Schema::hasColumn('products', 'order_count') || !Schema::hasColumn('products', 'trending_score')) {
                return;
            }

            $table->index('trending_score', 'products_trending_score_idx');
            $table->index('view_count', 'products_view_count_idx');
            $table->index('order_count', 'products_order_count_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'trending_score')) {
                $table->dropIndex('products_trending_score_idx');
            }
            if (Schema::hasColumn('products', 'view_count')) {
                $table->dropIndex('products_view_count_idx');
            }
            if (Schema::hasColumn('products', 'order_count')) {
                $table->dropIndex('products_order_count_idx');
            }

            foreach ([
                'last_trending_calculation',
                'trending_score',
                'order_count',
                'view_count',
            ] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
