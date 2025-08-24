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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_methods');
    }
};
