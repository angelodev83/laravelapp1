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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('ndc')->nullable();
            $table->unsignedInteger('stock_size')->nullable();
            $table->string('category')->nullable();
            $table->string('manufacturer')->nullable();
            $table->decimal('rx_price', 10, 2)->nullable(); 
            $table->decimal('340b_price', 10, 2)->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
