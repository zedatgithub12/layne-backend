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
        Schema::create('variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('name');
            $table->string('color');
            $table->string('size');
            $table->double('price');
            $table->integer('stock_quantity');
            $table->json('images')->nullable();
            $table->string('ar_file')->nullable();
            $table->enum('availability', ['local', 'pre-order', 'comingsoon'])->default('local');
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
