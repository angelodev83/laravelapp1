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
            $table->string('po_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('wholesaler_name')->nullable();
            $table->bigInteger('task_id')->index()->unsigned()->nullable();
            $table->bigInteger('status_id')->unsigned()->default(701)->nullable();
            $table->bigInteger('pharmacy_prescription_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drug_orders', function (Blueprint $table) {
            $table->dropColumn('po_name');
            $table->dropColumn('account_number');
            $table->dropColumn('wholesaler_name');
            $table->dropColumn('task_id');
            $table->dropColumn('status_id');
            $table->dropColumn('pharmacy_prescription_id');
        });
    }
};
