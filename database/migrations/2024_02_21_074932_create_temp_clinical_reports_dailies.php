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
        Schema::create('temp_clinical_reports_dailies', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('patient_name')->nullable();
            $table->text('patient_birthdate')->nullable();
            $table->text('medications')->nullable();
            $table->text('completed_date')->nullable();
            $table->text('date_of_interaction')->nullable();
            $table->text('date_of_initiation')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('date_side_effects')->nullable();
            $table->text('date_follow_up')->nullable();
            $table->text('recommended_vitamins')->nullable();
            $table->text('pdc_rate')->nullable();
            $table->text('outlier_type')->nullable();
            $table->text('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_clinical_reports_dailies');
    }
};
