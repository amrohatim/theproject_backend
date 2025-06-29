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
        // Add status field to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            // Add item-level status tracking
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
                  ->default('pending')
                  ->after('total')
                  ->comment('Status of this specific order item');
                  
            // Add fields for storing product specifications
            $table->json('specifications')->nullable()->after('status')
                  ->comment('Product specifications at time of order');
                  
            // Add fields for color and size selections
            $table->unsignedBigInteger('color_id')->nullable()->after('specifications')
                  ->comment('Selected product color ID');
            $table->string('color_name')->nullable()->after('color_id')
                  ->comment('Selected product color name');
            $table->string('color_value')->nullable()->after('color_name')
                  ->comment('Selected product color value/code');
            $table->string('color_image')->nullable()->after('color_value')
                  ->comment('Selected product color image');
                  
            $table->unsignedBigInteger('size_id')->nullable()->after('color_image')
                  ->comment('Selected product size ID');
            $table->string('size_name')->nullable()->after('size_id')
                  ->comment('Selected product size name');
            $table->string('size_value')->nullable()->after('size_name')
                  ->comment('Selected product size value');
        });

        // Create order_status_history table to track status changes
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->string('previous_status')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Create order_item_status_history table to track item status changes
        Schema::create('order_item_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->string('previous_status')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Add partially_shipped status to orders table
        Schema::table('orders', function (Blueprint $table) {
            // Modify the status enum to include partially_shipped
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'partially_shipped', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the status history tables
        Schema::dropIfExists('order_item_status_history');
        Schema::dropIfExists('order_status_history');

        // Revert the orders table status enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");

        // Remove the added columns from order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'specifications',
                'color_id',
                'color_name',
                'color_value',
                'color_image',
                'size_id',
                'size_name',
                'size_value',
            ]);
        });
    }
};
