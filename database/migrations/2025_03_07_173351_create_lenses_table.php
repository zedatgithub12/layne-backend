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
        Schema::create('lenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('lens_type_id');
            $table->enum('lens_material', ['still', 'iron', 'glass', 'plastic']);
            $table->string('lens_color');
            $table->string('lens_coating');
            $table->string('lens_power');
            $table->boolean('polarized')->default(false);
            $table->boolean('photochromatic')->default(false);
            $table->integer('lens_thickness');
            $table->text('description');
            $table->string('use_cases');
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();

            $table->foreign('lens_type_id')->references('id')->on('lens_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lenses');
    }
};
