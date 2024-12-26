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
        Schema::table('operation_rts', function (Blueprint $table) {
            $table->string('prescriber')->nullable();
            $table->string('pharmacist')->nullable();
            $table->string('prescribed_item_name')->nullable();
            $table->string('dispensed_item_ndc')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('primary_tp')->nullable();
            $table->string('secondary_tp')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('origin')->nullable();
            $table->string('lot_number')->nullable();
            $table->string('legacy_number')->nullable();
            $table->string('original_status_name')->nullable();
            $table->date('original_status_changed_date')->nullable();
            $table->string('transaction_status_name')->nullable();
            $table->date('transaction_status_changed_date')->nullable();
            $table->text('daw')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_rts', function (Blueprint $table) {
            //
        });
    }
};
