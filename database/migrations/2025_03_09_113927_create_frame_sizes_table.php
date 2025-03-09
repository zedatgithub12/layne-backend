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
        Schema::create('frame_sizes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('frame_id');
            $table->uuid('size_id');
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();

            // Foreign keys
            $table->foreign('frame_id')->references('id')->on('frames')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frame_sizes');
    }
};
