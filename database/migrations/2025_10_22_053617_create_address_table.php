<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->string('name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->integer('province_id')->nullable();
            $table->string('province_name', 100)->nullable();
            $table->integer('district_id')->nullable();
            $table->string('district_name', 100)->nullable();
           $table->string('ward_id', 20)->nullable();

            $table->string('ward_name', 100)->nullable();
            $table->string('address_detail', 255)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('address');
    }
};
