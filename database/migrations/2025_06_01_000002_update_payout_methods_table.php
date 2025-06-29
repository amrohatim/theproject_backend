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
        // Check if there are any foreign key constraints
        if (Schema::hasTable('payment_transactions')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                // Drop the foreign key constraint if it exists
                if (Schema::hasColumn('payment_transactions', 'payout_method_id')) {
                    $table->dropForeign(['payout_method_id']);
                    $table->dropColumn('payout_method_id');
                }
            });
        }

        if (Schema::hasTable('payout_preferences')) {
            Schema::table('payout_preferences', function (Blueprint $table) {
                // Drop the foreign key constraint if it exists
                if (Schema::hasColumn('payout_preferences', 'default_payout_method_id')) {
                    $table->dropForeign(['default_payout_method_id']);
                    $table->dropColumn('default_payout_method_id');
                }
            });
        }

        // Drop the existing table if it exists
        Schema::dropIfExists('payout_methods');

        // Create a new payout_methods table with improved structure
        Schema::create('payout_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider_type'); // 'stripe', 'paypal', etc.
            $table->string('payout_type'); // 'bank_account', 'paypal', etc.
            $table->string('name');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('last_four')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('account_type')->nullable(); // 'checking', 'savings', etc.
            $table->string('account_holder_name')->nullable();
            $table->string('account_holder_type')->nullable(); // 'individual', 'company'
            $table->string('currency')->default('USD');
            $table->string('country')->nullable();
            $table->string('payout_email')->nullable(); // For PayPal or other email-based methods
            $table->string('token_id')->nullable(); // For storing payout tokens from providers
            $table->string('external_account_id')->nullable(); // For storing external account IDs from providers
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->json('meta_data')->nullable(); // Additional data
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes for better record keeping
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_methods');

        // Recreate the original table structure
        Schema::create('payout_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'bank_account', 'paypal', etc.
            $table->string('name');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('last_four')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('account_type')->nullable(); // 'checking', 'savings', etc.
            $table->string('email')->nullable(); // For PayPal
            $table->boolean('is_default')->default(false);
            $table->json('meta_data')->nullable(); // Additional data
            $table->timestamps();
        });
    }
};
