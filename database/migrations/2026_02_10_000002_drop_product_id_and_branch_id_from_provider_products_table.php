<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('provider_products')) {
            return;
        }

        if (Schema::hasColumn('provider_products', 'product_id')) {
            $fk = DB::selectOne(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'provider_products'
                   AND COLUMN_NAME = 'product_id'
                   AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            if ($fk && isset($fk->CONSTRAINT_NAME)) {
                DB::statement("ALTER TABLE `provider_products` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            }

            $idx = DB::selectOne(
                "SELECT INDEX_NAME
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'provider_products'
                   AND INDEX_NAME != 'PRIMARY'
                   AND COLUMN_NAME IN ('provider_id','product_id')
                 GROUP BY INDEX_NAME
                 HAVING COUNT(DISTINCT COLUMN_NAME) = 2"
            );
            if ($idx && isset($idx->INDEX_NAME)) {
                DB::statement("ALTER TABLE `provider_products` DROP INDEX `{$idx->INDEX_NAME}`");
            }

            Schema::table('provider_products', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }

        if (Schema::hasColumn('provider_products', 'branch_id')) {
            $fk = DB::selectOne(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'provider_products'
                   AND COLUMN_NAME = 'branch_id'
                   AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            if ($fk && isset($fk->CONSTRAINT_NAME)) {
                DB::statement("ALTER TABLE `provider_products` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            }

            Schema::table('provider_products', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('provider_products', function (Blueprint $table) {
            if (!Schema::hasColumn('provider_products', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            }

            if (!Schema::hasColumn('provider_products', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            }

            if (Schema::hasColumn('provider_products', 'product_id')) {
                $table->unique(['provider_id', 'product_id']);
            }
        });
    }
};
