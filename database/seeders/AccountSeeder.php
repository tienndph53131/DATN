<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role_id' => 1,
                'status' => 1
            ],
            [
                'name' => 'Nhân viên 1',
                'email' => 'nv1@gmail.com',
                'password' => Hash::make('nv12345'),
                'role_id' => 3,
                'status' => 1
            ],
            [
                'name' => 'Nhân viên 2',
                'email' => 'nv2@gmail.com',
                'password' => Hash::make('nv12345'),
                'role_id' => 3,
                'status' => 1
            ],
        ];
        foreach ($accounts as $accountData) {
            Account::updateOrCreate(['email' => $accountData['email']], $accountData);
        }
    }
}