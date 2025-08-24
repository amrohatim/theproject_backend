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
        // Update the registration_step enum to support the new flow
        DB::statement("ALTER TABLE users MODIFY COLUMN registration_step ENUM(
            'pending',
            'info_completed',
            'email_verification_pending',
            'email_verified',
            'phone_verification_pending', 
            'phone_verified',
            'company_completed',
            'license_completed',
            'verified'
        ) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN registration_step ENUM(
            'pending',
            'info_completed',
            'company_completed',
            'license_completed',
            'verified'
        ) DEFAULT 'pending'");
    }
};
