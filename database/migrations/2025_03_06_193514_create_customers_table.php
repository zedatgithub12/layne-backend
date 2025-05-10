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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->id('user_id');
            $table->string('avatar')->nullable();
            $table->string('name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birthdate')->nullable();
            $table->string('phone_number')->unique();
            $table->string('email')->nullable()->unique();
            $table->integer('otp_code');
            $table->dateTime('otp_expires_at');
            $table->boolean('is_verified')->default(false);
            $table->text('shipping_address');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
