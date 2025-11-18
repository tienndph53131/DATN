<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddWarehouseRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert a 'warehouse' role if not exists
        $exists = DB::table('roles')->where('name', 'warehouse')->exists();
        if (! $exists) {
            DB::table('roles')->insert([
                'name' => 'warehouse',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('roles')->where('name', 'warehouse')->delete();
    }
}
