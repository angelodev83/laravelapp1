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
        Schema::create('patient_jot_forms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('patient_id')->index();
            $table->string('uid');
            $table->string('form_id')->nullable();
            $table->string('ip')->nullable();
            $table->datetime('jf_created_at')->nullable();
            $table->string('status')->nullable();
            $table->smallInteger('new')->default(0);

            $table->string('current_primary_care_provider')->nullable();
            $table->string('current_preferred_pharmacy')->nullable();
            $table->string('other_current_healthcare_providers')->nullable();
            $table->string('is_current_or_past_patient_at_ctclusi_dental_clinic')->nullable();
            $table->string('is_ctclusi_tribal_member')->nullable();
            $table->string('has_health_insurance')->nullable();
            $table->string('current_insurance_provider')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_jot_forms');
    }
};
