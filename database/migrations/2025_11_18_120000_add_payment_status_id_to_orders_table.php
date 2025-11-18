<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_status_id')) {
                $table->unsignedBigInteger('payment_status_id')->default(1)->after('status_id');
                // add FK if payment_status table exists
                if (Schema::hasTable('payment_status')) {
                    $table->foreign('payment_status_id')->references('id')->on('payment_status')->onDelete('set null');
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_status_id')) {
                // drop foreign key if exists (best-effort)
                try {
                    $table->dropForeign(['payment_status_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('payment_status_id');
            }
        });
    }
};
