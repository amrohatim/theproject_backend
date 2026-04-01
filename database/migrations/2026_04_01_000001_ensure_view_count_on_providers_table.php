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
        if (!Schema::hasTable('providers')) {
            return;
        }

        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'view_count')) {
                $table->integer('view_count')->default(0)->after('total_ratings');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('providers')) {
            return;
        }

        Schema::table('providers', function (Blueprint $table) {
            if (Schema::hasColumn('providers', 'view_count')) {
                $table->dropColumn('view_count');
            }
        });
    }
};
