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
        Schema::create('oig__exclusion__lists', function (Blueprint $table) {
            $table->id();
            $table->string('lastname')->nullable();
            $table->string('firstname')->nullable();
            $table->string('midname')->nullable();
            $table->string('busname')->nullable();
            $table->string('general')->nullable();
            $table->string('specialty')->nullable();
            $table->string('upin')->nullable();
            $table->string('npi')->nullable();
            $table->string('dob')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('excltype')->nullable();
            $table->string('excldate')->nullable();
            $table->string('reindate')->nullable();
            $table->string('waiverdate')->nullable();
            $table->string('wvrstate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oig__exclusion__lists');
    }
};
