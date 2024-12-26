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
        Schema::create('pharmacy_staff', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pharmacy_store_id')->index()->unsigned();
            $table->foreign('pharmacy_store_id')
                ->references('id')
                ->on('pharmacy_stores')
                ->onDelete('cascade');
            $table->bigInteger('employee_id')->index()->unsigned();
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
            $table->text('schedule')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_staff');
    }
};
