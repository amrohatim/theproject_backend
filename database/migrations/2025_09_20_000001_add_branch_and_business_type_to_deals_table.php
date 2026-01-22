<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('deals')) {
            return;
        }

        Schema::table('deals', function (Blueprint $table) {
            if (!Schema::hasColumn('deals', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('deals', 'business_type_id')) {
                $table->foreignId('business_type_id')
                    ->nullable()
                    ->constrained('business_types')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('deals')) {
            return;
        }

        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'business_type_id')) {
                $table->dropForeign(['business_type_id']);
                $table->dropColumn('business_type_id');
            }
            if (Schema::hasColumn('deals', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
    }
};
