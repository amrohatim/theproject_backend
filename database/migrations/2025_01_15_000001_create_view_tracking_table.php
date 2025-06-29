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
        // Check if the table already exists
        if (!Schema::hasTable('view_tracking')) {
            Schema::create('view_tracking', function (Blueprint $table) {
                $table->id();
                $table->string('entity_type'); // 'vendor', 'branch', 'provider', 'category'
                $table->unsignedBigInteger('entity_id');
                $table->unsignedBigInteger('user_id')->nullable(); // null for anonymous users
                $table->string('session_id')->nullable(); // for anonymous users
                $table->string('device_fingerprint')->nullable(); // for anonymous users
                $table->string('ip_address', 45);
                $table->string('user_agent')->nullable();
                $table->timestamp('viewed_at');
                $table->timestamps();

                // Indexes for performance
                $table->index(['entity_type', 'entity_id']);
                $table->index(['user_id', 'entity_type', 'entity_id']);
                $table->index(['session_id', 'entity_type', 'entity_id']);
                $table->index(['device_fingerprint', 'entity_type', 'entity_id']);
                $table->index(['ip_address', 'entity_type', 'entity_id']);
                $table->index('viewed_at');

                // Composite index for unique view checking
                $table->index(['entity_type', 'entity_id', 'user_id', 'viewed_at']);
                $table->index(['entity_type', 'entity_id', 'session_id', 'viewed_at']);
                $table->index(['entity_type', 'entity_id', 'device_fingerprint', 'viewed_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_tracking');
    }
};
