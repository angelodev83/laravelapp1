<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('eod_cashes', function (Blueprint $table) {
            $table->decimal('total_cash_received', 12, 2)->nullable()->after('date');
        });
        DB::statement('ALTER TABLE eod_cashes CHANGE `cash_deposited_to_bank` total_cash_deposited_to_bank DECIMAL(12, 2) DEFAULT NULL');
        DB::statement('ALTER TABLE eod_cashes CHANGE `cash_register_amount` total_check_received DECIMAL(12, 2) DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eod_cashes', function (Blueprint $table) {
            //
        });
    }
};
