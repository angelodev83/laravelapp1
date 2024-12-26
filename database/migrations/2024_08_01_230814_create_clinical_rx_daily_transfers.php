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
        Schema::create('clinical_rx_daily_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->unsigned()->nullable();
            $table->date('date')->nullable()->index();
            $table->bigInteger('patient_id')->unsigned()->nullable()->index();

            $table->date('date_called')->nullable();
            $table->string('medication_description')->nullable();
            $table->string('previous_pharmacy')->nullable();
            $table->string('provider')->nullable();
            $table->string('is_patient_seen_at_trhc')->nullable();
            $table->string('call_status')->nullable();
            $table->string('transfer_to_trp')->nullable();
            $table->string('fax_pharmacy')->nullable();
            $table->string('is_ma')->nullable();
            $table->string('expected_rx')->nullable();
            $table->text('remarks')->nullable();

            $table->string('status')->default('in_progress')->nullable();

            $table->string('patient_name')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('phone_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_rx_daily_transfers');
    }
};
