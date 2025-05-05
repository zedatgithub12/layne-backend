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
            $table->id('user_id');
            $table->string('order_number')->unique();
            $table->uuid('frame_id');
            $table->string('frame_name');
            $table->uuid('variant_id');
            $table->string('lens');
            $table->string('lens_type')->nullable();
            $table->string('lens_variant_name')->nullable();
            $table->string('lens_variant_value')->nullable();
            $table->boolean('need_prescription')->nullable();
            $table->string('prescription')->nullable();
            $table->float('total_price');
            $table->string('shipping_address')->nullable();
            $table->string('shipping_method')->nullable();
            $table->enum('payment_status', ['pending', 'completed'])->default('pending');
            $table->string('delivery_confirmation_code')->unique();
            $table->enum('delivery_status', ['pending', 'picked', 'on-delivery', 'blocked', 'delivered', 'cancelled'])->default('pending');
            $table->enum('status', ['processing', 'cancelled', 'completed'])->default('processing');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
