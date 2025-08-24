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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('address');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->string('status')->default('active'); // active, inactive, pending
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('opening_hours')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
