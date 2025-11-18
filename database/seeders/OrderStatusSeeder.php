<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'Pending'],
            ['status_name' => 'Processing'],
            ['status_name' => 'Shipped'],
            ['status_name' => 'Completed'],
            ['status_name' => 'Cancelled'],
        ];

        foreach ($statuses as $s) {
            DB::table('order_status')->updateOrInsert(['status_name' => $s['status_name']], $s);
        }
    }
}
