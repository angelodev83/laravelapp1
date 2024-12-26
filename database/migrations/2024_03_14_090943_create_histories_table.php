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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('value');
            $table->integer('order_id')->nullable();
            $table->integer('monthly_revenue_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('file_id')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->integer('inmar_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
