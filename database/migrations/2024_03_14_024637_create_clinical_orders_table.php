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
        Schema::create('clinical_orders', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('patient_id');
            $table->bigInteger('order_number')->nullable()->default(20);
            
            //Attachments
            $table->unsignedBigInteger('rx_image')->nullable();
            $table->foreign('rx_image')->references('id')->on('files')->where('document_type', 'rx_image');

            $table->unsignedBigInteger('intake_form')->nullable();
            $table->foreign('intake_form')->references('id')->on('files')->where('document_type', 'intake_form');

            $table->unsignedBigInteger('pod_proof_of_delivery')->nullable();
            $table->foreign('pod_proof_of_delivery')->references('id')->on('files')->where('document_type', 'pod');

            $table->string('shipment_type')->nullable(); //USPS or FedEx
            $table->string('shipment_tracking_number')->nullable();
            $table->integer('shipment_status_id')->nullable();

            $table->date('order_date')->nullable();
            $table->unsignedBigInteger('clinic_id');
            $table->foreign('clinic_id')->references('id')->on('clinics');
            $table->text('comments')->nullable();

            $table->unsignedBigInteger('pharmacy_store_id');
            $table->foreign('pharmacy_store_id')->references('id')->on('pharmacy_stores');
            $table->unsignedBigInteger('user_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_orders');
    }
};
