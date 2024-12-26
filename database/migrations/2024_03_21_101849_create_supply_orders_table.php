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
        Schema::create('supply_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->text('comments')->nullable();
            $table->date('order_date')->nullable()->default(now());
            $table->string('order_by')->nullable();
            $table->string('shipment_type')->nullable();
            $table->string('shipment_tracking_number')->nullable();
            $table->unsignedBigInteger('shipment_status_id')->default(301);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('pharmacy_store_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_orders');
    }
};
