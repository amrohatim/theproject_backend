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
        Schema::table('payout_methods', function (Blueprint $table) {
            // Remove the deleted_at column
            $table->dropSoftDeletes();

            // Rename columns back to original names
            $table->renameColumn('payout_type', 'type');
            $table->renameColumn('payout_email', 'email');

            // Drop new columns
            $table->dropColumn([
                'provider_type',
                'account_holder_name',
                'account_holder_type',
                'currency',
                'country',
                'token_id',
                'external_account_id',
                'is_verified',
                'verified_at'
            ]);
        });
    }
};
