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
        Schema::create('clinical_therapy_change_and_reco', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->unsigned()->nullable();
            $table->date('date')->nullable()->index();
            $table->bigInteger('patient_id')->unsigned()->nullable()->index();
            
            $table->string('last_provider_that_sent_rx')->nullable();
            $table->string('medication_description')->nullable();
            $table->string('recommendation')->nullable();
            $table->text('remarks')->nullable();
            $table->string('patient_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_therapy_change_and_reco');
    }
};
