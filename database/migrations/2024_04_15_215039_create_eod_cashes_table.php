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
        Schema::create('eod_cashes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('cash_deposited_to_bank', 12, 2);
            $table->decimal('cash_register_amount', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eod_cashes');
    }
};
