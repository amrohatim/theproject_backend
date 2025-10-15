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
        Schema::create('provider_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_type_id');
            $table->unsignedBigInteger('provider_id');
            $table->enum('status', ['active', 'inactive', 'cancelled'])->default('active')->notNullable();
            $table->date('start_at')->notNullable();
            $table->date('end_at')->notNullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('subscription_type_id')
                  ->references('id')
                  ->on('subscription_types')
                  ->onDelete('cascade');

            $table->foreign('provider_id')
                  ->references('id')
                  ->on('providers')
                  ->onDelete('cascade');

            // Indexes for performance
            $table->index('provider_id');
            $table->index('status');
            $table->index(['provider_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_subscriptions');
    }
};

