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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_method')->default('vendor')
                  ->comment("Assigned shipping method: 'vendor' or 'aramex'");
            $table->string('shipping_status')->default('pending')
                  ->comment('Delivery status (e.g. pending, shipped, delivered)');
            $table->decimal('shipping_cost', 10, 2)->default(0)
                  ->comment('Cost of shipping');
            $table->string('tracking_number')->nullable()
                  ->comment('Tracking number for the shipment');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_method',
                'shipping_status',
                'shipping_cost',
                'tracking_number',
                'customer_name',
                'customer_phone',
                'shipping_city',
                'shipping_country'
            ]);
        });
    }
};
