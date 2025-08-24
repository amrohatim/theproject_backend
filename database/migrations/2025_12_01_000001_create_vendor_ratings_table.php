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
        Schema::create('vendor_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating')->unsigned()->comment('Rating from 1 to 5');
            $table->text('review_text')->nullable();
            $table->timestamps();

            // Ensure one rating per customer per vendor
            $table->unique(['customer_id', 'vendor_id']);

            // Add indexes for performance
            $table->index(['vendor_id', 'rating']);
            $table->index(['customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_ratings');
    }
};
