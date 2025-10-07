<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->date('birthday')->nullable();
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->enum('sex', ['male', 'female', 'other'])->nullable();
            $table->string('address')->nullable();
            $table->string('password');
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('accounts');
    }
};
