<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('comments')) return;
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'rating')) {
                $table->tinyInteger('rating')->default(0)->after('content');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('comments')) return;
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }
};
