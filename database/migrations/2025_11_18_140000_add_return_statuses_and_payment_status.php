<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Insert order status 'Hoàn hàng' if not exists
        try {
            $exists = DB::table('order_status')->where('status_name', 'Hoàn hàng')->exists();
            if (! $exists) {
                DB::table('order_status')->insert([
                    'status_name' => 'Hoàn hàng',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // ignore if table not present in this environment
            logger()->warning('Could not insert Hoàn hàng order status: ' . $e->getMessage());
        }

        // Insert payment status 'Đã hoàn tiền' if not exists
        try {
            $exists2 = DB::table('payment_status')->where('status_name', 'Đã hoàn tiền')->exists();
            if (! $exists2) {
                DB::table('payment_status')->insert([
                    'status_name' => 'Đã hoàn tiền',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            logger()->warning('Could not insert Đã hoàn tiền payment status: ' . $e->getMessage());
        }
    }

    public function down()
    {
        try {
            DB::table('order_status')->where('status_name', 'Hoàn hàng')->delete();
        } catch (\Throwable $_) {}

        try {
            DB::table('payment_status')->where('status_name', 'Đã hoàn tiền')->delete();
        } catch (\Throwable $_) {}
    }
};
