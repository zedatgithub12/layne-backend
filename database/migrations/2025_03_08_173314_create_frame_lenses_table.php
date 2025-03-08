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
        Schema::create('frame_lenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('frame_id');
            $table->uuid('lens_id');
            $table->boolean('is_default')->default(false);
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('frame_id')->references('id')->on('frames')->onDelete('cascade');
            $table->foreign('lens_id')->references('id')->on('lenses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frame_lenses');
    }
};
