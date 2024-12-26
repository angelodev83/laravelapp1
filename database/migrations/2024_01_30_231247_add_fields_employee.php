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
         Schema::table('employees', function (Blueprint $table) {
            $table->integer('user_id')->default(0);
            $table->text('nickname')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('contact_number')->nullable();
            $table->text('address')->nullable();
            $table->enum('employment_type',['Full Time' ,'Part Time'])->default('Full Time');
            $table->text('company')->nullable();
            $table->enum('compensationtype',['Salaried'])->default('Salaried');
            $table->text('annual_salary')->nullable();
            $table->text('manager')->nullable();
            $table->text('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('nickname');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('contact_number');
            $table->dropColumn('address');
            $table->dropColumn('employment_type');
            $table->dropColumn('company');
            $table->dropColumn('compensationtype');
            $table->dropColumn('annual_salary');
            $table->dropColumn('manager');
            $table->dropColumn('image');
        });
    }
};
