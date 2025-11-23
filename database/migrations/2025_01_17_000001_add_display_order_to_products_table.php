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
        if (Schema::hasColumn('products', 'display_order')) {
            return;
        }

        $afterColumn = Schema::hasColumn('products', 'is_multi_branch') ? 'is_multi_branch' : 'is_available';

        Schema::table('products', function (Blueprint $table) use ($afterColumn) {
            $table->integer('display_order')->default(0)->after($afterColumn);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'display_order')) {
                $table->dropColumn('display_order');
            }
        });
    }
};
