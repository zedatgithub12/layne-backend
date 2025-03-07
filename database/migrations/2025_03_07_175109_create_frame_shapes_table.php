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
        Schema::create('frame_shapes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('rim_type');
            $table->integer('bridge_width');
            $table->integer('temple_length');
            $table->integer('lens_width');
            $table->enum('frame_material', ['iron', 'plastic', 'wood']);
            $table->string('face_shape_suitability');
            $table->enum('status', ['available', 'out-of-stock', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frame_shapes');
    }
};
