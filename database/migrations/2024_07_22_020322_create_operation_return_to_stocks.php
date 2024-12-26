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
        Schema::create('operation_rts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('patient_id')->index()->unsigned();
            $table->string('rx_number');
            $table->date('fill_date');
            $table->integer('call_attempts');
            $table->bigInteger('status_id')->default(921);
            $table->bigInteger('user_id')->index()->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->index()->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_return_to_stocks');
    }
};
