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
        Schema::table('pharmacy_service_satisfaction_surveys', function (Blueprint $table) {
            $table->text('what_would_have_made_experience_5_stars')->nullable();
            $table->text('suggestions_for_improvement')->nullable();
            $table->string('reason_of_call_or_visit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacy_service_satisfaction_surveys', function (Blueprint $table) {
            //
        });
    }
};
