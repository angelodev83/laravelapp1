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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->integer('order_number')->nullable();
            
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
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
