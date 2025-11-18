<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFinanceRole extends Migration
{
    public function up()
    {
        $exists = DB::table('roles')->where('name', 'finance')->exists();
        if (! $exists) {
            DB::table('roles')->insert([
                'name' => 'finance',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('roles')->where('name', 'finance')->delete();
    }
}
