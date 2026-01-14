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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->integer('salary')->nullable();
            $table->text('nice_to_have')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->foreignId('type')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('type_other')->nullable();
            $table->integer('number_of_applications')->default(0);
            $table->boolean('onsite')->default(false);
            $table->string('location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
