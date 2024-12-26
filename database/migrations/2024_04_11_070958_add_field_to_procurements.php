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
        Schema::table('drug_orders', function (Blueprint $table) {
            $table->bigInteger('wholesaler_id')->nullable();
        });
        Schema::table('supply_orders', function (Blueprint $table) {
            $table->bigInteger('wholesaler_id')->nullable();
        });
        Schema::table('inmars', function (Blueprint $table) {
            $table->bigInteger('wholesaler_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drug_orders', function (Blueprint $table) {
            $table->dropColumn('wholesaler_id');
        });
        Schema::table('supply_orders', function (Blueprint $table) {
            $table->dropColumn('wholesaler_id');
        });
        Schema::table('inmars', function (Blueprint $table) {
            $table->dropColumn('wholesaler_id');
        });
    }
};
