<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfer_task_status_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('transfer_task_status_id');
            $table->integer('transfer_task_id')->nullable();
            $table->datetime('change_at')->nullable();
            $table->datetime('change_from')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_task_status_logs');
    }
};
