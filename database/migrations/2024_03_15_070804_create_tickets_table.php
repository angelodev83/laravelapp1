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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('subtext')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedInteger('pharmacy_store_id');
            $table->unsignedInteger('status_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('assigned_to_employee_id')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
