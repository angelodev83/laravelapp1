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
        Schema::create('gross_sales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('pharmacy_store_id')->unsigned()->nullable();
            $table->bigInteger('patient_id')->unsigned()->nullable();
            $table->string('transmit_type')->nullable();
            $table->text('third_party_name')->nullable();
            $table->date('transaction_date')->nullable();
            $table->string('status_code')->nullable();
            $table->decimal('third_party_amount', 10, 2)->nullable();
            $table->decimal('third_party_tax_amount', 10, 2)->nullable();
            $table->decimal('third_party_total_amount', 10, 2)->nullable();
            $table->decimal('copay_amount', 10, 2)->nullable();
            $table->decimal('copay_tax_amount', 10, 2)->nullable();
            $table->decimal('copay_total_amount', 10, 2)->nullable();
            $table->decimal('flat_fee', 10, 2)->nullable();
            $table->decimal('script_amount', 10, 2)->nullable();
            $table->decimal('script_sales_tax_amount', 10, 2)->nullable();
            $table->string('is_taxable', 25)->nullable();
            $table->decimal('acquisition_cost', 10, 2)->nullable();
            $table->decimal('estimated_dir_fee', 10, 2)->nullable();
            $table->decimal('gross_profit', 10, 2)->nullable();
            $table->string('drug_name')->nullable();
            $table->string('ndc')->nullable();
            $table->string('rx_number')->nullable();
            $table->integer('refill_number')->nullable();
            $table->string('pharmacist')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('patient_name')->nullable();
            $table->string('card_holder_id')->nullable();
            $table->string('third_party_bin')->nullable();
            $table->string('prescriber_name')->nullable();
            $table->string('card_holder_name')->nullable();
            $table->decimal('dispensing_fee', 10, 2)->nullable();
            $table->string('dispensed_inventory_group')->nullable();
            $table->decimal('uc_amount', 10, 2)->nullable();
            $table->string('brand_group')->nullable();
            $table->integer('net_rx_count')->nullable();
            $table->date('date_filled')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gross_sales');
    }
};
