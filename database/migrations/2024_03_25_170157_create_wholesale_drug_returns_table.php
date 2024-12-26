<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->down();
        Schema::create('wholesale_drug_returns', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable();
            $table->date('date_filed')->nullable();
            $table->string('bin')->nullable();
            $table->text('reject_comments')->nullable();
            $table->smallInteger('is_compound')->nullable()->default(0);
            $table->bigInteger('user_id')->index()->unsigned();
            $table->bigInteger('pharmacy_store_id')->index()->unsigned();
            $table->bigInteger('patient_id')->nullable();
            $table->string('prescriber_name')->nullable();
            $table->string('order_by')->nullable();
            $table->string('shipment_type')->nullable();
            $table->string('shipment_tracking_number')->nullable();
            $table->string('service')->nullable();
            $table->string('package')->nullable();
            $table->string('size')->nullable();
            $table->unsignedBigInteger('from_pharmacy_store_id')->nullable();
            $table->unsignedBigInteger('to_pharmacy_store_id')->nullable();
            $table->unsignedBigInteger('shipment_status_id')->default(301);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_drug_returns');
    }
};
