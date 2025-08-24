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
        Schema::table('users', function (Blueprint $table) {
            // Add fields for enhanced registration
            $table->boolean('phone_verified')->default(false)->after('phone');
            $table->timestamp('phone_verified_at')->nullable()->after('phone_verified');
            $table->enum('registration_step', ['pending', 'info_completed', 'company_completed', 'license_completed', 'verified'])->default('pending')->after('email_verified_at');
            $table->json('registration_data')->nullable()->after('registration_step'); // Store temporary registration data
            
            // Add indexes for performance
            $table->index(['phone', 'phone_verified']);
            $table->index(['registration_step', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone', 'phone_verified']);
            $table->dropIndex(['registration_step', 'role']);
            $table->dropColumn([
                'phone_verified',
                'phone_verified_at',
                'registration_step',
                'registration_data'
            ]);
        });
    }
};
