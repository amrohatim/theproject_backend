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
        Schema::table('deals', function (Blueprint $table) {
            // Arabic language support columns
            if (!Schema::hasColumn('deals', 'title_arabic')) {
                $table->string('title_arabic')->nullable()->after('title')
                    ->comment('Arabic translation of the deal title');
            }

            if (!Schema::hasColumn('deals', 'description_arabic')) {
                $table->text('description_arabic')->nullable()->after('title_arabic')
                    ->comment('Arabic translation of the deal description');
            }

            if (!Schema::hasColumn('deals', 'promotional_message_arabic')) {
                $table->string('promotional_message_arabic', 50)->nullable()->after('description_arabic')
                    ->comment('Arabic translation of the promotional message');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'promotional_message_arabic')) {
                $table->dropColumn('promotional_message_arabic');
            }
            if (Schema::hasColumn('deals', 'description_arabic')) {
                $table->dropColumn('description_arabic');
            }
            if (Schema::hasColumn('deals', 'title_arabic')) {
                $table->dropColumn('title_arabic');
            }
        });
    }
};
