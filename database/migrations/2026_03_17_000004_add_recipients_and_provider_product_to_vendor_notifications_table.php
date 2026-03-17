<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vendor_notifications')) {
            return;
        }

        Schema::table('vendor_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_notifications', 'recipient_type')) {
                $table->enum('recipient_type', ['vendor', 'provider', 'merchant'])
                    ->default('vendor')
                    ->after('is_opened');
            }

            if (!Schema::hasColumn('vendor_notifications', 'recipient_id')) {
                $table->unsignedBigInteger('recipient_id')->nullable()->after('recipient_type');
                $table->index(['recipient_type', 'recipient_id'], 'vendor_notifications_recipient_idx');
            }

            if (!Schema::hasColumn('vendor_notifications', 'provider_product_id')) {
                $table->foreignId('provider_product_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('provider_products')
                    ->nullOnDelete();
            }
        });

        // Backfill recipient fields from existing vendor_id rows.
        DB::table('vendor_notifications')
            ->whereNull('recipient_id')
            ->whereNotNull('vendor_id')
            ->update([
                'recipient_type' => 'vendor',
                'recipient_id' => DB::raw('vendor_id'),
            ]);

        // Make vendor_id nullable so non-vendor recipients can be stored.
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            try {
                Schema::table('vendor_notifications', function (Blueprint $table) {
                    $table->dropForeign(['vendor_id']);
                });
            } catch (\Throwable $e) {
                // Ignore if FK name differs or already dropped.
            }

            DB::statement('ALTER TABLE vendor_notifications MODIFY vendor_id BIGINT UNSIGNED NULL');

            try {
                Schema::table('vendor_notifications', function (Blueprint $table) {
                    $table->foreign('vendor_id')->references('id')->on('companies')->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // Ignore duplicate FK errors.
            }
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE vendor_notifications ALTER COLUMN vendor_id DROP NOT NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('vendor_notifications')) {
            return;
        }

        Schema::table('vendor_notifications', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_notifications', 'provider_product_id')) {
                $table->dropConstrainedForeignId('provider_product_id');
            }

            if (Schema::hasColumn('vendor_notifications', 'recipient_id')) {
                $table->dropIndex('vendor_notifications_recipient_idx');
                $table->dropColumn('recipient_id');
            }

            if (Schema::hasColumn('vendor_notifications', 'recipient_type')) {
                $table->dropColumn('recipient_type');
            }
        });
    }
};
