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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('license_type')->default('registration'); // registration, business, etc.
            $table->string('license_file_path'); // Path to uploaded PDF file
            $table->string('license_file_name'); // Original filename
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days'); // Duration in days
            $table->enum('status', ['active', 'expired', 'pending', 'rejected'])->default('pending');
            $table->date('renewal_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['status', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
