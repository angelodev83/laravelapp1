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
        Schema::create('temp_pioneer_patients', function (Blueprint $table) {
            $table->id();
            $table->text('rx_number')->nullable();
            $table->text('patient_full_name_last_then_first')->nullable();
            $table->text('prescriber_full_name_last_then_first')->nullable();
            $table->text('prescribed_item')->nullable();
            $table->text('dispensed_item_name')->nullable();
            $table->text('date_filled')->nullable();
            $table->text('current_transaction_status_date')->nullable();
            $table->text('current_transaction_status')->nullable();
            $table->text('date_written')->nullable();
            $table->text('expiration_date')->nullable();
            $table->text('dea_schedule')->nullable();
            $table->text('rx_status')->nullable();
            $table->text('rx_status_changed_on')->nullable();
            $table->text('written_quantity')->nullable();
            $table->text('pay_method')->nullable();
            $table->text('pricing_method')->nullable();
            $table->text('primary')->nullable();
            $table->text('secondary')->nullable();
            $table->text('origin')->nullable();
            $table->text('priority')->nullable();
            $table->text('daw')->nullable();
            $table->text('pharmacist')->nullable();
            $table->text('dispensed_item_inventory_group')->nullable();
            $table->text('primary_remit_amount')->nullable();
            $table->text('changed_on')->nullable();
            $table->text('refills_remaining')->nullable();
            $table->text('refill_number')->nullable();
            $table->text('dispensed_quantity')->nullable();
            $table->text('days_supply')->nullable();
            $table->text('dispensing_fee_submitted')->nullable();
            $table->text('dispensing_fee_paid')->nullable();
            $table->text('patient_paid_amount')->nullable();
            $table->text('acquisition_cost')->nullable();
            $table->text('gross_profit')->nullable();
            $table->text('patient_primary_address')->nullable();
            $table->text('patient_primary_city')->nullable();
            $table->text('patient_primary_state')->nullable();
            $table->text('patient_primary_zip_code')->nullable();
            $table->text('patient_primary_phone_number')->nullable();
            $table->text('patient_date_of_birth')->nullable();
            $table->text('dispensed_item_ndc')->nullable();
            $table->text('facility_name')->nullable();
            $table->text('diagnosis_icd9_code')->nullable();
            $table->text('diagnosis_disease_id')->nullable();
            $table->text('label_type')->nullable();
            $table->text('dispensed_drug_class')->nullable();
            $table->text('prescriber_dea')->nullable();
            $table->text('prescriber_primary_address')->nullable();
            $table->text('prescriber_primary_city')->nullable();
            $table->text('prescriber_primary_state')->nullable();
            $table->text('prescriber_primary_zip')->nullable();
            $table->text('prescriber_primary_phone')->nullable();
            $table->text('refill_or_new')->nullable();
            $table->text('primary_group_number')->nullable();
            $table->text('data_entry_on')->nullable();
            $table->text('primary_third_party_bin')->nullable();
            $table->text('secondary_third_party_bin')->nullable();
            $table->text('prescriber_type')->nullable();
            $table->text('primary_third_party_pcn')->nullable();
            $table->text('prescriber_npi')->nullable();
            $table->text('pharmacy_name')->nullable();
            $table->text('pharmacy_ncpdp')->nullable();
            $table->text('prescriber_specialization')->nullable();
            $table->text('primary_copay_amount')->nullable();
            $table->text('secondary_remit_amount')->nullable();
            $table->text('completed_on')->nullable();
            $table->text('prescriber_fax_number')->nullable();
            $table->text('tracking_number')->nullable();
            $table->text('shipper_name')->nullable();
            $table->text('turnaround_time_business_days')->nullable();
            $table->text('patient_email')->nullable();
            $table->text('sale_receipt_number')->nullable();
            $table->text('patient_delivery_address')->nullable();
            $table->text('patient_delivery_city')->nullable();
            $table->text('patient_delivery_state')->nullable();
            $table->text('patient_delivery_zip')->nullable();
            $table->text('patient_days_supply_ends')->nullable();
            $table->text('prescriber_state_license_number')->nullable();
            $table->text('pharmacy_npi')->nullable();
            $table->text('completed_workflow_status')->nullable();
            $table->text('turnaround_time_hours')->nullable();
            $table->text('prescriber_email')->nullable();
            $table->text('app_store_unique_batch')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_pioneer_patients');
    }
};
