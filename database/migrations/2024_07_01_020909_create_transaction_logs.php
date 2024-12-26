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
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pharmacy_store_id')->nullable();
            $table->bigInteger('page_id')->index()->nullable();
            $table->bigInteger('user_id')->index()->nullable();
            $table->longText('data')->nullable();
            $table->string('function')->nullable();
            $table->string('module_name')->nullable();
            $table->bigInteger('module_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('action')->default('created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_log');
    }
};
