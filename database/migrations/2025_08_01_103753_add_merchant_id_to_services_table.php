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
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->nullable()->after('branch_id');
            $table->foreign('merchant_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('merchant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropIndex(['merchant_id']);
            $table->dropColumn('merchant_id');
        });
    }
};
