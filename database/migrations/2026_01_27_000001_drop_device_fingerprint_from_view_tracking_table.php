<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('view_tracking')) {
            return;
        }

        if (!Schema::hasColumn('view_tracking', 'device_fingerprint')) {
            return;
        }

        // Drop indexes referencing device_fingerprint if they exist.
        $indexRows = DB::select('SHOW INDEX FROM view_tracking');
        $indexes = array_map(
            static fn ($row) => $row->Key_name ?? '',
            $indexRows
        );

        Schema::table('view_tracking', function (Blueprint $table) use ($indexes) {
            if (in_array('view_tracking_device_fingerprint_entity_type_entity_id_index', $indexes, true)) {
                $table->dropIndex('view_tracking_device_fingerprint_entity_type_entity_id_index');
            }
            if (in_array('vt_entity_device_viewed_idx', $indexes, true)) {
                $table->dropIndex('vt_entity_device_viewed_idx');
            }

            $table->dropColumn('device_fingerprint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('view_tracking')) {
            return;
        }

        Schema::table('view_tracking', function (Blueprint $table) {
            if (!Schema::hasColumn('view_tracking', 'device_fingerprint')) {
                $table->string('device_fingerprint')->nullable();
                $table->index(['device_fingerprint', 'entity_type', 'entity_id']);
                $table->index(['entity_type', 'entity_id', 'device_fingerprint', 'viewed_at'], 'vt_entity_device_viewed_idx');
            }
        });
    }
};
