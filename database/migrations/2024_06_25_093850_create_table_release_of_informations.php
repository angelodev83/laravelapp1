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
        Schema::create('release_of_information', function (Blueprint $table) {
            $table->id();

            $table->string('jot_form_uid')->nullable();
            $table->string('jot_form_id')->nullable();
            $table->string('jot_form_ip')->nullable();
            $table->string('jot_form_created_at')->nullable();
            $table->string('jot_form_status')->nullable();
            
            $table->string('hereby_authorize_person')->nullable();
            $table->string('hereby_authorize_person_name')->nullable();
            $table->string('hereby_authorize_person_address')->nullable();
            $table->string('hereby_authorize_person_phone_number')->nullable();
            $table->string('hereby_authorize_person_fax_number')->nullable();
            $table->string('to_person')->nullable();
            $table->string('to_person_name')->nullable();
            $table->string('to_person_address')->nullable();
            $table->string('to_person_phone_number')->nullable();
            $table->string('to_person_fax_number')->nullable();
            $table->text('information_to_data')->nullable();
            $table->text('purpose')->nullable();
            $table->date('expiration_date')->nullable();
            $table->text('patient_firstname')->nullable();
            $table->text('patient_lastname')->nullable();
            $table->text('patient_birth_date')->nullable();
            $table->date('signed_date')->nullable();
            $table->string('relationship_to_patient')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_of_information');
    }
};
