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
        Schema::table('operation_orders', function (Blueprint $table) {
            $table->integer('is_completed')->default(0)->after('user_id');
            $table->date('delivered-date')->nullable()->after('shipped_date');
            $table->date('labeled_date')->nullable()->after('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_orders', function (Blueprint $table) {
            //
        });
    }
};
