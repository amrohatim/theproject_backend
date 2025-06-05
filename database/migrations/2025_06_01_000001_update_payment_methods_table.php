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
                // Drop the foreign key constraint
                $table->dropForeign(['payment_method_id']);
                // Drop the column
                $table->dropColumn('payment_method_id');
            });
        }

        // Drop the existing table if it exists
        Schema::dropIfExists('payment_methods');

        // Create a new payment_methods table with improved structure
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider_type'); // 'stripe', 'paypal', etc.
            $table->string('payment_type'); // 'credit_card', 'bank_account', 'paypal', etc.
            $table->string('name');
            $table->string('card_brand')->nullable(); // 'visa', 'mastercard', 'amex', etc.
            $table->string('last_four')->nullable();
            $table->string('expiry_month')->nullable();
            $table->string('expiry_year')->nullable();
            $table->string('billing_email')->nullable(); // For PayPal or other email-based methods
            $table->string('billing_address_line1')->nullable();
            $table->string('billing_address_line2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('token_id')->nullable(); // For storing payment tokens from providers
            $table->string('customer_id')->nullable(); // For storing customer IDs from providers
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
        Schema::dropIfExists('payment_methods');

        // Recreate the original table structure
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'credit_card', 'paypal', etc.
            $table->string('name');
            $table->string('card_type')->nullable(); // 'visa', 'mastercard', etc.
            $table->string('last_four')->nullable();
            $table->string('expiry_month')->nullable();
            $table->string('expiry_year')->nullable();
            $table->string('email')->nullable(); // For PayPal
            $table->boolean('is_default')->default(false);
            $table->json('meta_data')->nullable(); // Additional data
            $table->timestamps();
        });
    }
};
