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
        if (!Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type'); // credit_card, paypal, etc.
                $table->string('name');
                $table->string('card_type')->nullable(); // visa, mastercard, etc.
                $table->string('last_four', 4)->nullable();
                $table->string('expiry_month', 2)->nullable();
                $table->string('expiry_year', 4)->nullable();
                $table->string('email')->nullable();
                $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('payment_methods');
    }
};
