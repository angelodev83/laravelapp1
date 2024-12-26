<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('dosage')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('days_supply')->nullable();
            $table->integer('refills_remaining')->nullable();
        });
    }

    public function down()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['dosage', 'qty', 'days_supply', 'refills_remaining']);
        });
    }
};
