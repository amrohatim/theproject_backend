<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maintenance', function (Blueprint $table) {
            $table->text('message_arabic')->nullable()->after('message');
        });

        DB::table('maintenance')
            ->whereNull('message_arabic')
            ->update(['message_arabic' => DB::raw('message')]);

        Schema::table('maintenance', function (Blueprint $table) {
            $table->text('message_arabic')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance', function (Blueprint $table) {
            $table->dropColumn('message_arabic');
        });
    }
};
