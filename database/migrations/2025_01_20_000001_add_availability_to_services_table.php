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
        $shouldAddAvailableDays = !Schema::hasColumn('services', 'available_days');
        $shouldAddStartTime = !Schema::hasColumn('services', 'start_time');
        $shouldAddEndTime = !Schema::hasColumn('services', 'end_time');

        if (! $shouldAddAvailableDays && ! $shouldAddStartTime && ! $shouldAddEndTime) {
            return;
        }

        $availableDaysAfter = Schema::hasColumn('services', 'home_service') ? 'home_service' : 'is_available';

        Schema::table('services', function (Blueprint $table) use (
            $shouldAddAvailableDays,
            $shouldAddStartTime,
            $shouldAddEndTime,
            $availableDaysAfter
        ) {
            if ($shouldAddAvailableDays) {
                $table->json('available_days')
                    ->nullable()
                    ->after($availableDaysAfter)
                    ->comment('Array of available days (0=Sunday, 1=Monday, etc.)');
            }

            if ($shouldAddStartTime) {
                $table->time('start_time')
                    ->nullable()
                    ->after('available_days')
                    ->comment('Service start time');
            }

            if ($shouldAddEndTime) {
                $table->time('end_time')
                    ->nullable()
                    ->after('start_time')
                    ->comment('Service end time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = array_filter(
            ['available_days', 'start_time', 'end_time'],
            fn ($column) => Schema::hasColumn('services', $column)
        );

        if (! empty($columns)) {
            Schema::table('services', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
