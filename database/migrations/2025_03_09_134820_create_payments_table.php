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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->float('amount');
            $table->string('payment_method');
            $table->string('transaction_id')->unique();
            $table->string('currency');
            $table->float('gateway_fee')->nullable();
            $table->boolean('refund_status')->nullable();
            $table->float('refund_amount')->nullable();
            $table->date('refund_date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'refunded'])->default('pending');
            $table->timestamps();

            // Foreign key relation
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
