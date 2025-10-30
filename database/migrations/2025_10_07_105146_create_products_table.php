<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
<<<<<<< HEAD
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('view')->default(0);
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
=======
            $table->string('image')->nullable();
            $table->integer('view')->default(0);
            $table->date('date')->nullable();
            $table->text('description')->nullable();
>>>>>>> origin/tien
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
