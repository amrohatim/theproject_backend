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
            if (!Schema::hasColumn('provider_products', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('product_id');
                $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_products', function (Blueprint $table) {
            if (Schema::hasColumn('provider_products', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
    }
};
