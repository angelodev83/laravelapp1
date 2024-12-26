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
        Schema::create('account_receivables', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pharmacy_store_id')->index()->nullable();
            $table->bigInteger('user_id')->index()->nullable();
            $table->integer('import_excel_file_id')->nullable();
            $table->date('as_of_date')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('primary_phone')->nullable();
            $table->date('date_last_payment')->nullable(); // 
            $table->decimal('amount_last_payment')->nullable();
            $table->decimal('amount_credit_limit')->nullable();
            $table->decimal('amount_new_charges')->nullable();
            $table->decimal('amount_invoiced_less_than_30')->nullable();
            $table->decimal('amount_30_days')->nullable();
            $table->decimal('amount_60_days')->nullable();
            $table->decimal('amount_90_days')->nullable();
            $table->decimal('amount_120_days')->nullable();
            $table->decimal('amount_unreconciled')->nullable();
            $table->decimal('amount_total_balance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_receivables');
    }
};
