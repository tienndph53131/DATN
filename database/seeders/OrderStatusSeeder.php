<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'Chưa xác nhận'],
            ['status_name' => 'Đã thanh toán, chờ xác nhận'],
            ['status_name' => 'Đã xác nhận'],
            ['status_name' => 'Chưa thanh toán'],
            ['status_name' => 'Đang chuẩn bị hàng'],
            ['status_name' => 'Đang giao'],
            ['status_name' => 'Đã giao'],
            ['status_name' => 'Đã nhận'],
            ['status_name' => 'Thành công'],
            ['status_name' => 'Hoàn hàng'],
            ['status_name' => 'Hủy đơn hàng'],
        ];

        foreach ($statuses as $s) {
            DB::table('order_status')->updateOrInsert(['status_name' => $s['status_name']], $s);
        }
    }
}
