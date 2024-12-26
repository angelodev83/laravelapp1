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
        Schema::create('clinical_rx_daily_census', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->unsigned()->nullable();
            $table->date('date')->nullable()->index();
            $table->bigInteger('patient_id')->unsigned()->nullable()->index();

            $table->string('patient_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->bigInteger('scripts_received')->nullable();
            $table->string('provider')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rx_daily_census');
    }
};
