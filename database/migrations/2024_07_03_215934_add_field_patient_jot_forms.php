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
        Schema::table('patient_jot_forms', function (Blueprint $table) {
            $table->string('is_head_of_household')->nullable();
            $table->text('insurance_policy_image_url')->nullable();
            $table->date('policy_holder_birth_date')->nullable();
            $table->string('school_based_health_center_affiliation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_jot_forms', function (Blueprint $table) {
            //
        });
    }
};
