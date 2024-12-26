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
        Schema::create('pharmacy_staff_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->bigInteger('pharmacy_staff_id')->index();
            $table->bigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('pharmacy_staff_schedules_daily', function (Blueprint $table) {
            $table->id();
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->smallInteger('week_day')->default(1); // 1 = monday = date('N')
            $table->bigInteger('pharmacy_staff_schedule_id')->index();
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_schedules');
        Schema::dropIfExists('employee_schedule_daily');
    }
};
