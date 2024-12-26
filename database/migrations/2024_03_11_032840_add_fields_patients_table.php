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
        Schema::table('patients', function (Blueprint $table) {
            $table->text('ssn')->nullable();
            $table->text('mrn')->nullable();
            $table->text('home_phone')->nullable();
            $table->text('work_phone')->nullable();
            $table->text('prefix')->nullable();
            $table->text('suffix')->nullable();
            $table->text('referral_source')->nullable();
            $table->text('referring_provider_fullname')->nullable();
            $table->text('referring_provider_id')->nullable();
            $table->text('practice_name')->nullable();
            $table->text('practice_id')->nullable();
            $table->text('middlename')->nullable();
            $table->text('last_appointment_date')->nullable();
            $table->text('last_diagnosis')->nullable();
            $table->text('last_encounter_date')->nullable();
            $table->text('emergency_phone')->nullable();
            $table->text('emergency_name')->nullable();
            $table->text('employer_name')->nullable();
            $table->text('employment_status')->nullable();
            $table->text('default_case_id')->nullable();
            $table->text('default_case_name')->nullable();
            $table->text('default_case_payer_scenario')->nullable();
            $table->text('default_case_send_patient_statement')->nullable();
            $table->text('dcc_related_to_abuse')->nullable();
            $table->text('dcc_related_to_auto_accident')->nullable();
            $table->text('dcc_related_to_auto_accident_state')->nullable();
            $table->text('dcc_related_to_epsdt')->nullable();
            $table->text('dcc_related_to_emergency')->nullable();
            $table->text('dcc_related_to_employment')->nullable();
            $table->text('dcc_related_to_family_planning')->nullable();
            $table->text('dcc_related_to_other')->nullable();
            $table->text('dcc_related_to_pregnancy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('ssn');
            $table->dropColumn('mrn');
            $table->dropColumn('home_phone');
            $table->dropColumn('work_phone');
            $table->dropColumn('prefix');
            $table->dropColumn('suffix');
            $table->dropColumn('referral_source');
            $table->dropColumn('referring_provider_fullname');
            $table->dropColumn('referring_provider_id');
            $table->dropColumn('practice_name');
            $table->dropColumn('practice_id');
            $table->dropColumn('middlename');
            $table->dropColumn('last_appointment_date');
            $table->dropColumn('last_diagnosis');
            $table->dropColumn('last_encounter_date');
            $table->dropColumn('emergency_phone');
            $table->dropColumn('emergency_name');
            $table->dropColumn('employer_name');
            $table->dropColumn('employment_status');
            $table->dropColumn('default_case_id');
            $table->dropColumn('default_case_name');
            $table->dropColumn('default_case_payer_scenario');
            $table->dropColumn('default_case_send_patient_statement');
            $table->dropColumn('dcc_related_to_abuse');
            $table->dropColumn('dcc_related_to_auto_accident');
            $table->dropColumn('dcc_related_to_auto_accident_state');
            $table->dropColumn('dcc_related_to_epsdt');
            $table->dropColumn('dcc_related_to_emergency');
            $table->dropColumn('dcc_related_to_employment');
            $table->dropColumn('dcc_related_to_family_planning');
             $table->dropColumn('dcc_related_to_other');
            $table->dropColumn('dcc_related_to_pregnancy');
        });
    }
};
