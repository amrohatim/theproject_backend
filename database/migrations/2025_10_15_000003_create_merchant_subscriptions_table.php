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
        Schema::create('merchant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_type_id')->comment('Foreign key to subscription_types table');
            $table->unsignedBigInteger('merchant_id')->comment('Foreign key to merchants table');
            $table->enum('status', ['active', 'inactive', 'cancelled'])->default('active')->comment('Subscription status');
            $table->date('start_at')->comment('Subscription start date');
            $table->date('end_at')->comment('Subscription end date');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('subscription_type_id')
                  ->references('id')
                  ->on('subscription_types')
                  ->onDelete('cascade');
            
            $table->foreign('merchant_id')
                  ->references('id')
                  ->on('merchants')
                  ->onDelete('cascade');

            // Indexes for performance
            $table->index('merchant_id', 'merchant_subscriptions_merchant_id_idx');
            $table->index('status', 'merchant_subscriptions_status_idx');
            $table->index(['merchant_id', 'status'], 'merchant_subscriptions_merchant_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_subscriptions');
    }
};

