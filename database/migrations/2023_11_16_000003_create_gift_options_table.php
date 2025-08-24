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
        Schema::create('gift_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->boolean('is_gift')->default(false);
            $table->boolean('gift_wrap')->default(false);
            $table->decimal('gift_wrap_price', 10, 2)->default(0);
            $table->string('gift_wrap_type')->nullable();
            $table->text('gift_message')->nullable();
            $table->string('gift_from')->nullable();
            $table->string('gift_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_options');
    }
};
