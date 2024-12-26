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
        Schema::create('patient_jot_form_prescription_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('patient_id')->index()->nullable();
            $table->string('uid')->index()->nullable();
            $table->string('form_id')->index()->nullable();
            $table->string('ip')->nullable();
            $table->datetime('jf_created_at')->nullable();
            $table->string('status')->nullable();
            $table->tinyInteger('new')->default(0)->nullable();

            $table->string('group_affiliated')->nullable();
            $table->string('preferred_form_of_communication')->nullable();
            $table->string('current_pharmacy')->nullable();
            $table->string('current_pharmacy_phone_number')->nullable();
            $table->string('current_pharmacy_address')->nullable();
            $table->string('current_pharmacy_address2')->nullable();
            $table->string('current_pharmacy_city')->nullable();
            $table->string('current_pharmacy_state')->nullable();
            $table->string('current_pharmacy_zip')->nullable();

            $table->string('prescriber_firstname')->nullable();
            $table->string('prescriber_lastname')->nullable();
            $table->string('prescriber_phone_number')->nullable();
            $table->string('prescriber_fax_number')->nullable();

            $table->string('medication_drug_name')->nullable();
            $table->string('medication_strength')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_jot_form_prescription_transfers');
    }
};
