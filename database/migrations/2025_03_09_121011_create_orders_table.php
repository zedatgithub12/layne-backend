<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->string('order_number')->unique();
            $table->float('total_price');
            $table->string('shipping_address');
            $table->string('shipping_method')->nullable();
            $table->enum('payment_status', ['pending', 'completed'])->default('pending');
            $table->string('delivery_confirmation_code')->unique();
            $table->enum('delivery_status', ['pending', 'picked', 'on-delivery', 'blocked', 'delivered', 'cancelled'])->default('pending');
            $table->enum('status', ['processing', 'cancelled', 'completed'])->default('processing');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
