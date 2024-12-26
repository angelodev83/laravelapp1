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
        $this->down();
        Schema::table('eod_deposits', function (Blueprint $table) {
            $table->bigInteger('pharmacy_store_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eod_deposits', function (Blueprint $table) {
            Schema::dropIfExists('phramcy_store_id');
        });
    }
};
