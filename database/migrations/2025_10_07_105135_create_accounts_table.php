<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('avatar')->nullable();
            $table->date('birthday')->nullable();
            $table->string('email', 100)->unique();
            $table->string('phone', 20)->nullable();
            $table->enum('sex', ['male', 'female', 'other'])->nullable();
            $table->string('password');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('accounts');
    }
};
