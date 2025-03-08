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
        Schema::create('frames', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand');
            $table->text('description')->nullable();
            $table->uuid('category_id');
            $table->double('weight')->default(0);
            $table->enum('gender', ['men', 'women', 'both']);
            $table->double('price');
            $table->double('discount_price')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('featured')->default(false);
            $table->json('images')->nullable();
            $table->json('tags')->nullable();
            $table->float('ratings')->default(0);
            $table->enum('status', ['available', 'out-of-stock', 'unavailable'])->default('available');
            $table->binary('try_on_asset')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frames');
    }
};
