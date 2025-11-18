<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('old_status_id')->nullable();
            $table->unsignedBigInteger('new_status_id')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('old_status_id');
            $table->index('new_status_id');

            // Add foreign keys if tables exist
            if (Schema::hasTable('orders')) {
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            }
            if (Schema::hasTable('order_status')) {
                $table->foreign('old_status_id')->references('id')->on('order_status')->onDelete('set null');
                $table->foreign('new_status_id')->references('id')->on('order_status')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
