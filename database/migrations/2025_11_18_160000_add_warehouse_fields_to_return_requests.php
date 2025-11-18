<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('return_requests', function (Blueprint $table) {
                // Neutralized: warehouse fields for return_requests removed as feature rolled back.
                // $table->boolean('warehouse_received')->default(false)->after('status');
                // $table->timestamp('received_at')->nullable()->after('warehouse_received');
        });
    }

    public function down()
    {
        // nothing (neutralized)
    }
};
