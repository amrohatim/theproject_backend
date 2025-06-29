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
        // Skip this migration as we've already created the table with soft deletes
        // in the previous migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Remove the deleted_at column
            $table->dropSoftDeletes();

            // Rename columns back to original names
            $table->renameColumn('provider_transaction_id', 'transaction_id');

            // Drop new columns
            $table->dropColumn([
                'transaction_uuid',
                'provider',
                'provider_status',
                'fee',
                'net_amount',
                'related_transaction_id',
                'order_id',
                'processed_at',
                'notes',
                'error_message'
            ]);
        });
    }
};
