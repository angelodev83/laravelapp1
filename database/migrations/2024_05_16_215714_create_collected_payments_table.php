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
        Schema::create('collected_payments', function (Blueprint $table) {
            $table->id();
            $table->text('account_number')->nullable();
            $table->text('account_name')->nullable();
            $table->text('primary_phone')->nullable();
            $table->date('last_payment_date')->nullable();
            $table->decimal('last_payment_amount', 12, 2)->nullable();
            $table->date('beginning_balance_date')->nullable();
            $table->decimal('beginning_balance_amount', 12, 2)->nullable();
            $table->decimal('running_balance_as_of_date', 12, 2)->nullable();
            $table->bigInteger('pharmacy_store_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collected_payments');
    }
};
