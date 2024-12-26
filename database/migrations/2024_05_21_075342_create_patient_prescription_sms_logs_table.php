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
        Schema::create('patient_prescription_sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('order_number'); // hidden comment {pioneer_id/serial_number}-{date}
            $table->bigInteger('rx_number')->nullable();
            $table->text('hidden_comment')->nullable();
            $table->string('status');
            $table->bigInteger('patient_id')->index();
            $table->bigInteger('rc_id')->index()->nullable(); // unique
            $table->bigInteger('user_id')->nullable();
            $table->text('sms_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_prescription_logs');
    }
};
