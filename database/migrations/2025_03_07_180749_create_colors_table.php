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
        Schema::create('colors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('color_code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_textured')->default(false);
            $table->boolean('is_mixed')->default(false);
            $table->json('mixed_colors')->nullable();
            $table->string('texture_image')->nullable();
            $table->json('tags')->nullable();
            $table->enum('status', ['published', 'draft', 'unpublished'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
