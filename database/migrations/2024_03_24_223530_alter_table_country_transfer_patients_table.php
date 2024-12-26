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
        Schema::table('transfer_patients', function (Blueprint $table) {
            $table->text('gender')->nullable()->change();
            $table->text('birthdate')->nullable()->change();
            $table->text('home_address')->nullable()->change();
            $table->text('city')->nullable()->change();
            $table->text('county')->nullable()->change();
            $table->text('state')->nullable()->change();
            $table->text('zip')->nullable()->change();
            $table->text('phone_number')->nullable()->change();
            $table->text('email')->nullable()->change();
            $table->text('affiliated')->nullable()->change();
            $table->text('communication')->nullable()->change();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
