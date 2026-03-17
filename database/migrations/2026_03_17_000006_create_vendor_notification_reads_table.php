<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_notification_id')
                ->constrained('vendor_notifications')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->timestamps();

            $table->unique(['vendor_notification_id', 'user_id'], 'vendor_notification_reads_unique');
            $table->index(['user_id', 'read_at'], 'vendor_notification_reads_user_read_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_notification_reads');
    }
};
