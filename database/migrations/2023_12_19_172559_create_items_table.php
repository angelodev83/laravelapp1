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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->text('name')->nullable(); //medication name
            $table->integer('qty')->nullable();
            $table->string('dosage')->nullable();
            $table->integer('days_supply')->nullable();
            $table->integer('refills_remaining')->nullable();
            $table->string('ndc')->nullable();
            
            // RX Stage - Pending Inprogress, Filled
            // RX Status - Waiting For Data Entry, Waiting for Fill, Waiting for Script,  Filled, Processed in Pioneer, IOU
            // Shipment Status - Shipped, In Transit, Received, Delivered, Cancelled, Returned, Replaced, Replaced and Returned, Replaced and Deliver
            $table->string('rx_stage')->nullable();
            $table->string('rx_status')->nullable();
            
            // Inventory Type RX or 340B
            $table->string('inventory_type')->nullable();
            $table->integer('is_rewrited')->default(0);
           

            //Todo: View for Orders nadumaan sa 340B - products SENT

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
