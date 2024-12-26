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
        Schema::create('clinical_kpis', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->bigInteger('patient_id')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('reason')->nullable();
            $table->integer('store_status_id')->nullable();
            $table->text('care_goals')->nullable();
            $table->text('biller')->nullable();
            $table->double('profits', 11, 2)->default(0);
            $table->integer('pharmacy_store_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_kpis');
    }
};
