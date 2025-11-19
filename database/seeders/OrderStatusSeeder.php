<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạm thời vô hiệu hóa kiểm tra khóa ngoại để tránh lỗi khi truncate
        Schema::disableForeignKeyConstraints();

        // SỬA LỖI: Dùng đúng tên bảng 'order_status' (số ít)
        DB::table('order_status')->truncate();

        // Bật lại kiểm tra khóa ngoại
        Schema::enableForeignKeyConstraints();

        $statuses = [
            // SỬA LỖI: Dùng đúng tên cột 'status_name'
            ['id' => 1, 'status_name' => 'Chưa xác nhận'],
            ['id' => 2, 'status_name' => 'Đã xác nhận'],
            ['id' => 3, 'status_name' => 'Đang chuẩn bị hàng'],
            ['id' => 4, 'status_name' => 'Đang giao'],
            ['id' => 5, 'status_name' => 'Đã giao'],
            ['id' => 6, 'status_name' => 'Đã nhận'],
            ['id' => 7, 'status_name' => 'Thành công'],
            ['id' => 8, 'status_name' => 'Hoàn hàng'],
            ['id' => 9, 'status_name' => 'Hủy đơn hàng'],
        ];

        // Thêm dữ liệu mới vào đúng bảng
        DB::table('order_status')->insert($statuses);
    }
}
