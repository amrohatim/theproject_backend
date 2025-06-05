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
        // Skip this migration if we've already dropped the tables in previous migrations
        if (!Schema::hasTable('payment_methods') || !Schema::hasTable('payout_methods')) {
            return;
        }

        // Drop the existing table if it exists
        Schema::dropIfExists('payment_transactions');

        // Create a new payment_transactions table with improved structure
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_uuid')->unique(); // Unique identifier for the transaction
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // 'payment', 'payout', 'refund', 'chargeback', etc.
            $table->string('status'); // 'pending', 'completed', 'failed', 'cancelled', etc.
            $table->string('provider'); // 'stripe', 'paypal', etc.
            $table->string('provider_transaction_id')->nullable(); // Transaction ID from the provider
            $table->string('provider_status')->nullable(); // Status from the provider
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0.00); // Transaction fee
            $table->decimal('net_amount', 10, 2); // Amount after fees
            $table->string('currency')->default('USD');
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payout_method_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('related_transaction_id')->nullable(); // For refunds, chargebacks, etc.
            $table->foreignId('order_id')->nullable(); // For order-related transactions
            $table->timestamp('processed_at')->nullable(); // When the transaction was processed
            $table->json('meta_data')->nullable(); // Additional data
            $table->text('notes')->nullable(); // Internal notes
            $table->text('error_message')->nullable(); // Error message if the transaction failed
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes for better record keeping
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');

        // Recreate the original table structure
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // 'payout', 'payment', 'refund', etc.
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('status'); // 'completed', 'pending', 'failed', etc.
            $table->string('transaction_id')->nullable(); // External transaction ID
            $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payout_method_id')->nullable()->constrained()->onDelete('set null');
            $table->json('meta_data')->nullable(); // Additional data
            $table->timestamps();
        });
    }
};
