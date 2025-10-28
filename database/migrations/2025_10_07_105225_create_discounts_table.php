<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // Mã giảm giá
            $table->text('description')->nullable(); // Mô tả
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent'); // Loại giảm giá
            $table->decimal('discount_value', 10, 2)->default(0); // Giá trị giảm
            $table->date('start_date')->nullable(); // Ngày bắt đầu
            $table->date('end_date')->nullable(); // Ngày kết thúc
            $table->boolean('active')->default(true); // Còn hoạt động không
            $table->decimal('minimum_order_amount', 10, 2)->nullable(); // Giá trị đơn hàng tối thiểu
            $table->integer('usage_limit')->nullable(); // Giới hạn lượt dùng
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('discounts');
    }
};
