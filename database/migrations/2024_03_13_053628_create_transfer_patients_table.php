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
        Schema::create('transfer_patients', function (Blueprint $table) {
            $table->id();
            $table->text('firstname');
            $table->text('lastname');
            $table->text('gender');
            $table->date('birthdate');
            $table->text('home_address');
            $table->text('city');
            $table->text('country');
            $table->text('state');
            $table->text('zip');
            $table->text('phone_number');
            $table->text('email');
            $table->text('affiliated');
            $table->text('communication');
            $table->text('current_pharmacy')->nullable();
            $table->text('pharmacy_phone_number')->nullable();
            $table->text('pharmacy_address')->nullable();
            $table->text('pharmacy_city')->nullable();
            $table->text('pharmacy_state')->nullable();
            $table->text('pharmacy_zip')->nullable();
            $table->text('prescriber_firstname')->nullable();
            $table->text('prescriber_lastname')->nullable();
            $table->text('prescriber_phone_number')->nullable();    
            $table->text('prescriber_fax_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_patients');
    }
};
