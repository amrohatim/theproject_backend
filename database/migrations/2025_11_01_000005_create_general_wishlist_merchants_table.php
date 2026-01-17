<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_wishlist_merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'merchant_id'], 'general_wishlist_merchants_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_wishlist_merchants');
    }
};
