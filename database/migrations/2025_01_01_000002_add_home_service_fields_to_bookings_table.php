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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'is_home_service')) {
                $table->boolean('is_home_service')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('bookings', 'service_location')) {
                $table
                    ->enum('service_location', ['provider', 'customer'])
                    ->nullable()
                    ->after('is_home_service');
            }

            if (!Schema::hasColumn('bookings', 'address')) {
                $table->text('address')->nullable()->after('customer_location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('bookings', 'service_location')) {
                $table->dropColumn('service_location');
            }

            if (Schema::hasColumn('bookings', 'is_home_service')) {
                $table->dropColumn('is_home_service');
            }
        });
    }
};
