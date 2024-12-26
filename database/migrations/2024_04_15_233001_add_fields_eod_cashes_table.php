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
        Schema::table('eod_cashes', function (Blueprint $table) {
            $table->integer('pharmacy_store_id')->nullable()->after('cash_register_amount');
            $table->integer('user_id')->nullable()->after('pharmacy_store_id');
         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
