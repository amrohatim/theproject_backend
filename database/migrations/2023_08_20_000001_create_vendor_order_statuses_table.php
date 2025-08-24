<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create vendor_order_statuses table to track vendor-specific statuses
        Schema::create('vendor_order_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('companies')->onDelete('cascade');
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])
                  ->default('pending')
                  ->comment('Status of this vendor\'s portion of the order');
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            
            // Add a unique constraint to ensure one status per vendor per order
            $table->unique(['order_id', 'vendor_id']);
        });

        // Create vendor_order_status_history table to track vendor status changes
        Schema::create('vendor_order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('companies')->onDelete('cascade');
            $table->string('status');
            $table->string('previous_status')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Update the orders table status enum to include more statuses
        Schema::table('orders', function (Blueprint $table) {
            // Modify the status enum to include more statuses
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'partially_shipped', 'shipped', 'partially_delivered', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the vendor status tables
        Schema::dropIfExists('vendor_order_status_history');
        Schema::dropIfExists('vendor_order_statuses');

        // Revert the orders table status enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'partially_shipped', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
