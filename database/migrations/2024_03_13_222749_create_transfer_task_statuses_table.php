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
        Schema::create('transfer_task_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer('transfer_patient_id');
            $table->integer('transfer_task_id')->nullable();
            $table->datetime('due_date')->nullable();
            $table->text('shipping_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_task_statuses');
    }
};
