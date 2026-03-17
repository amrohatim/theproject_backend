<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('provider_products') || !Schema::hasColumn('provider_products', 'status')) {
            return;
        }

        // Normalize old status values before applying enum constraint.
        DB::statement("\n            UPDATE provider_products\n            SET status = CASE\n                WHEN status = 'active' THEN 'approved'\n                WHEN status = 'inactive' THEN 'rejected'\n                WHEN status IS NULL OR TRIM(status) = '' THEN 'pending'\n                WHEN status NOT IN ('pending', 'approved', 'rejected') THEN 'pending'\n                ELSE status\n            END\n        ");

        DB::statement("\n            ALTER TABLE provider_products\n            MODIFY COLUMN status ENUM('pending', 'approved', 'rejected')\n            NOT NULL DEFAULT 'pending'\n        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('provider_products') || !Schema::hasColumn('provider_products', 'status')) {
            return;
        }

        DB::statement("\n            UPDATE provider_products\n            SET status = CASE\n                WHEN status = 'approved' THEN 'active'\n                WHEN status = 'rejected' THEN 'inactive'\n                ELSE 'pending'\n            END\n        ");

        DB::statement("\n            ALTER TABLE provider_products\n            MODIFY COLUMN status VARCHAR(255)\n            NOT NULL DEFAULT 'active'\n        ");
    }
};
