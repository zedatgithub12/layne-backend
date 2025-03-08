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
        Schema::create('frames_shapes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('frame_id');
            $table->uuid('shape_id');
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('frame_id')->references('id')->on('frames')->onDelete('cascade');
            $table->foreign('shape_id')->references('id')->on('frame_shapes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frames_shapes');
    }
};
