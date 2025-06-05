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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('awb_number')->nullable()
                  ->comment('Aramex airwaybill / tracking number');
            $table->string('status')->default('pending')
                  ->comment('Shipment status (e.g. pending, in_transit, delivered)');
            $table->json('shipment_details')->nullable()
                  ->comment('Additional shipment details from Aramex');
            $table->json('tracking_history')->nullable()
                  ->comment('Tracking history from Aramex');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
