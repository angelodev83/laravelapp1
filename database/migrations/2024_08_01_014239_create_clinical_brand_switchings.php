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
        Schema::create('clinical_brand_switchings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->unsigned()->nullable();
            $table->date('date')->nullable()->index();
            $table->bigInteger('patient_id')->unsigned()->nullable()->index();

            $table->string('rx_number')->nullable();
            $table->string('generic_medication_description')->nullable();
            $table->string('branded_medication_description')->nullable();
            $table->string('dispensed_medication_description')->nullable();
            $table->text('remarks')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('total_paid_claims', 15, 2)->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('status')->nullable();
            $table->string('patient_name')->nullable();
            $table->string('type')->default('IOU')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_brand_switchings');
    }
};
