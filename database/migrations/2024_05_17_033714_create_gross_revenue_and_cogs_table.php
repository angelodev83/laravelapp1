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
        Schema::create('gross_revenue_and_cogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pharmacy_store_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->integer('import_excel_file_id')->nullable();
            $table->bigInteger('rx_number')->nullable();
            $table->text('patient_fullname')->nullable();
            $table->text('prescriber_full_name')->nullable();
            $table->text('prescribed_item')->nullable();
            $table->text('dispensed_item_name')->nullable();
            $table->date('date_filed')->nullable();
            $table->date('date_written')->nullable();
            $table->text('rx_status')->nullable();
            $table->dateTime('rx_status_changed_on')->nullable();
            $table->text('written_quantity')->nullable();
            $table->text('pay_method')->nullable();
            $table->text('pricing_method')->nullable();
            $table->text('primary')->nullable();
            $table->text('secondary')->nullable();
            $table->text('origin')->nullable();
            $table->text('priority')->nullable();
            $table->text('daw')->nullable();
            $table->text('bin')->nullable();
            $table->text('current_transaction_status')->nullable();
            $table->dateTime('current_transaction_status_date')->nullable();
            $table->text('pharmacist')->nullable();
            $table->bigInteger('dispensed_quantity')->nullable();
            $table->bigInteger('days_supply')->nullable();
            $table->decimal('total_price_submitted', 12, 2)->nullable();
            $table->decimal('total_price_paid', 12, 2)->nullable();
            $table->decimal('patient_paid_amount', 12, 2)->nullable();
            $table->decimal('acquisition_cost', 12, 2)->nullable();
            $table->decimal('gross_profit', 12, 2)->nullable();
            $table->text('dispensed_item_ndc')->nullable();
            $table->text('facility_name')->nullable();
            $table->integer('dea_schedule')->nullable();
            $table->text('counseled_by_pharmacist')->nullable();
            $table->text('counseled_status')->nullable();
            $table->text('label_type')->nullable();
            $table->text('fill_requested_method')->nullable();
            $table->decimal('dispensed_awp', 12, 4)->nullable();
            $table->text('refill_or_new')->nullable();
            $table->dateTime('data_entry_on')->nullable();
            $table->text('prescriber_primary_category')->nullable();
            $table->decimal('secondary_remit_amount', 12, 2)->nullable();
            $table->decimal('primary_remit_amount', 12, 2)->nullable();
            $table->dateTime('completed_on')->nullable();
            $table->text('dispensed_item_inventory_group')->nullable();
            $table->text('tracking_number')->nullable();
            $table->text('shipper_name')->nullable();
            $table->decimal('dir_fee', 12, 2)->nullable();
            $table->dateTime('filled_on')->nullable();
            $table->text('transfer_type')->nullable();
            $table->text('transferred_from_pharmacy')->nullable();
            $table->text('transferred_to_pharmacy')->nullable();
            $table->decimal('turnaround_time_hours', 12, 2)->nullable();
            $table->decimal('rebate', 12, 2)->nullable();
            $table->text('gender')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gross_revenue_and_cogs');
    }
};
