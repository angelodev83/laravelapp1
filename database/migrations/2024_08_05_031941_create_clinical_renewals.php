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
        Schema::create('clinical_renewals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('patient_id')->index()->unsigned();
            $table->string('rx_number');
            $table->date('renew_date');
            $table->integer('call_attempts');
            $table->string('telebridge')->nullable();
            $table->text('reason_for_denial')->nullable();
            $table->bigInteger('status_id')->default(951);
            $table->bigInteger('user_id')->index()->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->index()->unsigned()->nullable();
            $table->tinyInteger('is_archived')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_renewals');
    }
};
