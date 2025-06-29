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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('business_type')->nullable();
            $table->string('registration_number')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('emirate')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->boolean('is_verified')->default(false);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('order_count')->default(0);
            $table->integer('merchant_score')->default(0)->index();
            $table->timestamp('last_score_calculation')->nullable();

            // UAE ID fields
            $table->string('uae_id_front')->nullable()->comment('Path to UAE ID front image');
            $table->string('uae_id_back')->nullable()->comment('Path to UAE ID back image');

            // Store location fields
            $table->decimal('store_location_lat', 10, 8)->nullable()->comment('Store latitude');
            $table->decimal('store_location_lng', 11, 8)->nullable()->comment('Store longitude');
            $table->string('store_location_address')->nullable()->comment('Store address from Google Maps');

            // Delivery capability fields
            $table->boolean('delivery_capability')->default(false);
            $table->json('delivery_fees')->nullable()->comment('Delivery fees by emirate');

            $table->timestamps();

            // Add indexes for performance
            $table->index(['status', 'is_verified']);
            $table->index(['average_rating']);
            $table->index(['emirate', 'city']);
            $table->index(['delivery_capability']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
