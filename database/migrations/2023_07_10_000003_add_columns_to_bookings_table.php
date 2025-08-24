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
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_id')->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->constrained()->onDelete('cascade');
                $table->string('booking_number')->unique();
                $table->date('booking_date');
                $table->time('booking_time');
                $table->integer('duration')->default(60); // in minutes
                $table->decimal('price', 10, 2)->default(0);
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
                $table->string('payment_method')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_id')->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->constrained()->onDelete('cascade');
                $table->string('booking_number')->unique();
                $table->date('booking_date');
                $table->time('booking_time');
                $table->integer('duration')->default(60); // in minutes
                $table->decimal('price', 10, 2)->default(0);
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
                $table->string('payment_method')->nullable();
                $table->text('notes')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::dropIfExists('bookings');
        }
    }
};
