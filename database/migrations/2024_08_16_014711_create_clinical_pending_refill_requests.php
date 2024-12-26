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
        Schema::create('clinical_pending_refill_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->unsigned()->nullable();
            $table->date('date')->nullable()->index();
            $table->bigInteger('patient_id')->unsigned()->nullable()->index();
            
            $table->string('patient_name')->nullable();
            $table->string('rx_number')->nullable();
            $table->string('medication_description')->nullable();
            $table->date('send_date')->nullable();
            $table->string('provider')->nullable();
            $table->string('status_name')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_pending_refill_requests');
    }
};
