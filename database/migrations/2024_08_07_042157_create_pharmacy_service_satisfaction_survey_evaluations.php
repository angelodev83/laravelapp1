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
        Schema::create('pharmacy_service_satisfaction_survey_evaluations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('survey_id')->index()->unsigned()->nullable();
            $table->text('question')->nullable();
            $table->string('answer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_service_satisfaction_survey_staff_evaluations');
    }
};
