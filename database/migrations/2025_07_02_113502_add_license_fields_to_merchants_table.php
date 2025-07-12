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
        Schema::table('merchants', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('merchants', 'license_file')) {
                $table->string('license_file')->nullable()->comment('Path to current license PDF file');
            }
            if (!Schema::hasColumn('merchants', 'license_expiry_date')) {
                $table->date('license_expiry_date')->nullable()->comment('License expiration date');
            }
            if (!Schema::hasColumn('merchants', 'license_status')) {
                $table->enum('license_status', ['verified', 'checking', 'expired', 'rejected'])->default('checking')->comment('License verification status');
            }
            if (!Schema::hasColumn('merchants', 'license_verified')) {
                $table->boolean('license_verified')->default(false)->comment('Whether license is currently valid and verified');
            }
            if (!Schema::hasColumn('merchants', 'license_rejection_reason')) {
                $table->text('license_rejection_reason')->nullable()->comment('Reason for license rejection');
            }
            if (!Schema::hasColumn('merchants', 'license_uploaded_at')) {
                $table->timestamp('license_uploaded_at')->nullable()->comment('When license was last uploaded');
            }
            if (!Schema::hasColumn('merchants', 'license_approved_at')) {
                $table->timestamp('license_approved_at')->nullable()->comment('When license was approved by admin');
            }
            if (!Schema::hasColumn('merchants', 'license_approved_by')) {
                $table->foreignId('license_approved_by')->nullable()->constrained('users')->comment('Admin who approved the license');
            }
        });

        // Add indexes separately to avoid conflicts
        try {
            Schema::table('merchants', function (Blueprint $table) {
                $table->index(['license_status', 'license_verified']);
            });
        } catch (Exception $e) {
            // Index might already exist, ignore
        }

        try {
            Schema::table('merchants', function (Blueprint $table) {
                $table->index(['license_expiry_date']);
            });
        } catch (Exception $e) {
            // Index might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropIndex(['license_status', 'license_verified']);
            $table->dropIndex(['license_expiry_date']);
            $table->dropForeign(['license_approved_by']);
            $table->dropColumn([
                'license_file',
                'license_expiry_date',
                'license_status',
                'license_verified',
                'license_rejection_reason',
                'license_uploaded_at',
                'license_approved_at',
                'license_approved_by'
            ]);
        });
    }
};
