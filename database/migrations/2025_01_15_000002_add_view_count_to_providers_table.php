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
        // Check if the providers table exists before trying to modify it
        if (Schema::hasTable('providers')) {
            Schema::table('providers', function (Blueprint $table) {
                // Add columns only if they don't already exist
                if (!Schema::hasColumn('providers', 'view_count')) {
                    $table->integer('view_count')->default(0)->after('total_ratings');
                }
                if (!Schema::hasColumn('providers', 'order_count')) {
                    $table->integer('order_count')->default(0)->after('view_count');
                }
                if (!Schema::hasColumn('providers', 'provider_score')) {
                    $table->integer('provider_score')->default(0)->index()->after('order_count');
                }
                if (!Schema::hasColumn('providers', 'last_score_calculation')) {
                    $table->timestamp('last_score_calculation')->nullable()->after('provider_score');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('providers')) {
            Schema::table('providers', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('providers', 'view_count')) {
                    $columnsToDrop[] = 'view_count';
                }
                if (Schema::hasColumn('providers', 'order_count')) {
                    $columnsToDrop[] = 'order_count';
                }
                if (Schema::hasColumn('providers', 'provider_score')) {
                    $columnsToDrop[] = 'provider_score';
                }
                if (Schema::hasColumn('providers', 'last_score_calculation')) {
                    $columnsToDrop[] = 'last_score_calculation';
                }

                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
