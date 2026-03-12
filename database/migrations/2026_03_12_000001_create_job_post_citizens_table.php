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
        Schema::create('job_post_citizens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->integer('salary');
            $table->text('nice_to_have');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('deadline');
            $table->string('type');
            $table->integer('number_of_applications');
            $table->boolean('onsite');
            $table->string('location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_post_citizens');
    }
};
