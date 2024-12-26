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
        Schema::create('clinical_outreaches', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->bigInteger('patient_id')->nullable();
            $table->integer('store_provider_status_id')->nullable();
            $table->text('reason')->nullable();
            $table->integer('in_charge')->nullable();
            $table->integer('store_call_status_id')->nullable();
            $table->time('time_start')->format('H:i')->nullable();
            $table->time('time_end')->format('H:i')->nullable();;
            $table->text('soap')->nullable();
            $table->integer('ses_adrs')->nullable();
            $table->integer('pharmacy_store_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_outreaches');
    }
};
