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
        Schema::create('customer_applied_citizens_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('job_citizens_id')->constrained('job_post_citizens')->cascadeOnDelete();
            $table->string('user_cv');
            $table->string('user_address');
            $table->string('user_phone');
            $table->string('user_email');
            $table->string('user_name');
            $table->string('password_image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_applied_citizens_jobs');
    }
};
