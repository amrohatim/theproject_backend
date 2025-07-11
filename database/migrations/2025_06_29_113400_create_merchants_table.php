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

            // License management fields
            $table->string('license_file')->nullable()->comment('Path to current license PDF file');
            $table->date('license_expiry_date')->nullable()->comment('License expiration date');
            $table->enum('license_status', ['verified', 'checking', 'expired', 'rejected'])->default('checking')->comment('License verification status');
            $table->boolean('license_verified')->default(false)->comment('Whether license is currently valid and verified');
            $table->text('license_rejection_reason')->nullable()->comment('Reason for license rejection');
            $table->timestamp('license_uploaded_at')->nullable()->comment('When license was last uploaded');
            $table->timestamp('license_approved_at')->nullable()->comment('When license was approved by admin');
            $table->foreignId('license_approved_by')->nullable()->constrained('users')->comment('Admin who approved the license');

            $table->timestamps();

            // Add indexes for performance
            $table->index(['status', 'is_verified']);
            $table->index(['average_rating']);
            $table->index(['emirate', 'city']);
            $table->index(['delivery_capability']);
            $table->index(['license_status', 'license_verified']);
            $table->index(['license_expiry_date']);
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
