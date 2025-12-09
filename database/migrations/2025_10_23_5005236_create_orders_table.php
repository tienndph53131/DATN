<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 50)->unique();
            
            // Liên kết với tài khoản đặt hàng
            $table->unsignedBigInteger('account_id');
            
            // Thông tin người nhận
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();

            // Liên kết tới bảng address
            $table->unsignedBigInteger('address_id')->nullable();

            // Các thông tin khác
            $table->dateTime('booking_date')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->text('note')->nullable();
            
            // Liên kết với bảng payment_methods và order_status, payment_status
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('payment_status_id')->default(1);
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('address_id')->references('id')->on('address')->onDelete('set null');
            $table->foreign('payment_id')->references('id')->on('payment_methods')->onDelete('set null');
            $table->foreign('status_id')->references('id')->on('order_status')->onDelete('set null');
             $table->foreign('payment_status_id')->references('id')->on('payment_status')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
