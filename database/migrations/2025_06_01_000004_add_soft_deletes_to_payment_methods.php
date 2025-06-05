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
        Schema::table('payment_methods', function (Blueprint $table) {
            // Remove the deleted_at column
            $table->dropSoftDeletes();

            // Rename columns back to original names
            $table->renameColumn('payment_type', 'type');
            $table->renameColumn('card_brand', 'card_type');
            $table->renameColumn('billing_email', 'email');

            // Drop new columns
            $table->dropColumn([
                'provider_type',
                'billing_address_line1',
                'billing_address_line2',
                'billing_city',
                'billing_state',
                'billing_postal_code',
                'billing_country',
                'token_id',
                'customer_id',
                'is_verified',
                'verified_at'
            ]);
        });
    }
};
