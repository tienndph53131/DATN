<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy vai trò admin
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();

        if ($adminRole) {
            DB::table('accounts')->insert([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('123456'), // Mật khẩu ví dụ
                'role_id' => $adminRole->id, // Lấy ID của vai trò Admin
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}