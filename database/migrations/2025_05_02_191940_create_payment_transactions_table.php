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
        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('transaction_type'); // payment, payout, refund, etc.
                $table->string('description')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('USD');
                $table->string('status'); // pending, completed, failed, etc.
                $table->string('transaction_id')->nullable(); // External transaction ID
                $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('payout_method_id')->nullable()->constrained()->onDelete('set null');
                $table->json('meta_data')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
