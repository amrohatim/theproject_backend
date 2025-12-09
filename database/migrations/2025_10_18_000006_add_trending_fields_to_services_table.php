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
        if (!Schema::hasTable('services')) {
            return;
        }

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'view_count')) {
                $table->integer('view_count')->default(0)->after('rating');
            }

            if (!Schema::hasColumn('services', 'order_count')) {
                $table->integer('order_count')->default(0)->after('view_count');
            }

            if (!Schema::hasColumn('services', 'trending_score')) {
                $table->integer('trending_score')->default(0)->after('order_count');
            }

            if (!Schema::hasColumn('services', 'last_trending_calculation')) {
                $table
                    ->timestamp('last_trending_calculation')
                    ->nullable()
                    ->after('trending_score');
            }

            if (Schema::hasColumn('services', 'trending_score')) {
                $table->index('trending_score', 'services_trending_score_idx');
            }
            if (Schema::hasColumn('services', 'view_count')) {
                $table->index('view_count', 'services_view_count_idx');
            }
            if (Schema::hasColumn('services', 'order_count')) {
                $table->index('order_count', 'services_order_count_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('services')) {
            return;
        }

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'trending_score')) {
                $table->dropIndex('services_trending_score_idx');
            }
            if (Schema::hasColumn('services', 'view_count')) {
                $table->dropIndex('services_view_count_idx');
            }
            if (Schema::hasColumn('services', 'order_count')) {
                $table->dropIndex('services_order_count_idx');
            }

            foreach ([
                'last_trending_calculation',
                'trending_score',
                'order_count',
                'view_count',
            ] as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
