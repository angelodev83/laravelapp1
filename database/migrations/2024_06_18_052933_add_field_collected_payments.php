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
            $table->date('payment_date')->nullable();
            $table->decimal('paid_amount')->nullable();
            $table->string('rx_number')->nullable();
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
