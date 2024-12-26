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
        Schema::create('pharmacy_service_satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->index()->nullable();
            $table->string('form_id')->index()->nullable();
            $table->string('ip')->nullable();
            $table->datetime('jf_created_at')->nullable();
            $table->string('status')->nullable();
            $table->tinyInteger('new')->default(0)->nullable();

            $table->string('lastname')->nullable();
            $table->string('firstname')->nullable();
            $table->string('email_address')->nullable();
            $table->string('pharmacist_name')->nullable();

            $table->smallInteger('how_was_service_today_score')->nullable();
            $table->smallInteger('how_satisfied_with_our_pharmacy_overall_score')->nullable();
            $table->smallInteger('pharmacist_or_patient_care_team_live_up_to_expectation_score')->nullable();
            $table->text('experience_feedback')->nullable();

            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_service_satisfaction_surveys');
    }
};
