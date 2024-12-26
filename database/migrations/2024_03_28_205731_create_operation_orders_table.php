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
        Schema::create('operation_orders', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name')->nullable();
            $table->string('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('rx_number')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('shipped_date')->nullable();
            $table->string('shipping_label');
            $table->string('status')->default('For Shipping Today')->nullable();
            $table->bigInteger('user_id')->index()->unsigned()->nullable();
            $table->bigInteger('document_id')->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->index()->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_orders');
    }
};
