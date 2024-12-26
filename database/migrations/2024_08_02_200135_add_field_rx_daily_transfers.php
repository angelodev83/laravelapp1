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
        Schema::table('clinical_rx_daily_transfers', function (Blueprint $table) {
            $table->string('is_transfer')->nullable();
            $table->string('is_received')->nullable();
            $table->tinyInteger('mail_sent_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinical_rx_daily_transfers', function (Blueprint $table) {
            //
        });
    }
};
