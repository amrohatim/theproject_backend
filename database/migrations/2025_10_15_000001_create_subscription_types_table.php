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
        Schema::create('subscription_types', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['vendor', 'merchant', 'provider'])->comment('User type for subscription');
            $table->enum('period', ['monthly', 'yearly'])->comment('Subscription billing period');
            $table->decimal('charge', 10, 2)->comment('Subscription amount to be paid');
            $table->string('title', 255)->nullable()->comment('Subscription plan title');
            $table->text('description')->nullable()->comment('Subscription plan description');
            $table->string('alert_message', 255)->nullable()->comment('Alert message for users');
            $table->timestamps();

            // Add indexes for query optimization
            $table->index('type', 'subscription_types_type_idx');
            $table->index('period', 'subscription_types_period_idx');
            $table->index(['type', 'period'], 'subscription_types_type_period_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_types');
    }
};

