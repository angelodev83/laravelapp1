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
        Schema::table('collected_payments', function (Blueprint $table) {
            $table->string('reconciling_account_name')->nullable();
            $table->date('pos_sales_date')->nullable();
            $table->date('posting_of_payment_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collected_payments', function (Blueprint $table) {
            //
        });
    }
};
