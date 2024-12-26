<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('UPDATE operation_orders SET shipped_date = STR_TO_DATE(shipped_date, "%Y-%m-%d")');
        Schema::table('operation_orders', function (Blueprint $table) {
            $table->date('shipped_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_orders', function (Blueprint $table) {
            $table->dropColumn('shipped_date');
        });
    }
};
