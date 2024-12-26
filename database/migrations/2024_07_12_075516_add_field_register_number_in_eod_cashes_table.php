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
            $table->text('register_number')->after('id')->nullable();
            $table->bigInteger('register_page_id')->default(65)->after('register_number')->nullable();
        });
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
