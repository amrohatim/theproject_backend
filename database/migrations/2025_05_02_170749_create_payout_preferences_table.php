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
        Schema::create('payout_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payout_frequency')->default('weekly'); // 'daily', 'weekly', 'biweekly', 'monthly'
            $table->decimal('minimum_payout_amount', 10, 2)->default(50.00);
            $table->string('currency')->default('USD');
            $table->foreignId('default_payout_method_id')->nullable()->constrained('payout_methods')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_preferences');
    }
};
