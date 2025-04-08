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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->json('categories');
            $table->string('shape');
            $table->json('lens_types');
            $table->text('description');
            $table->string('product_weight');
            $table->string('sku')->unique();
            $table->string('material');
            $table->string('pd_range');
            $table->string('rx_range');
            $table->string('spring_hinge');
            $table->string('bridge_fit');
            $table->boolean('adjustable_nose_pad');
            $table->boolean('is_flexible');
            $table->boolean('need_prescription');
            $table->json('tags');
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
