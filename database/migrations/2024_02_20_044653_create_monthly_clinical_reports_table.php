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
        Schema::create('monthly_clinical_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('clinical_id');
            $table->integer('value')->nullable();
            $table->integer('report_year')->nullable();
            $table->integer('report_month')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_clinical_reports');
    }
};
