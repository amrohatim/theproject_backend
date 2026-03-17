<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vendor_notifications') || !Schema::hasColumn('vendor_notifications', 'vendor_id')) {
            return;
        }

        Schema::table('vendor_notifications', function (Blueprint $table) {
            // Drop FK first if it exists.
            try {
                $table->dropForeign(['vendor_id']);
            } catch (\Throwable $e) {
                // Ignore when FK is missing or named differently.
            }

            $table->dropColumn('vendor_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('vendor_notifications') || Schema::hasColumn('vendor_notifications', 'vendor_id')) {
            return;
        }

        Schema::table('vendor_notifications', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('recipient_id')->constrained('companies')->nullOnDelete();
        });
    }
};
