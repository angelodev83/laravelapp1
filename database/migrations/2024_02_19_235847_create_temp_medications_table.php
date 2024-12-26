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
        Schema::create('temp_medications', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->text('ndc')->nullable();
            $table->text('package_size')->nullable();
            $table->text('balance_on_hand')->nullable();
            $table->text('therapeutic_class')->nullable();
            $table->text('category')->nullable();
            $table->text('manufacturer')->nullable();
            $table->text('rx_price')->nullable();
            $table->text('340b_price')->nullable();
            $table->text('awp_price')->nullable();
            $table->text('upc')->nullable();
            $table->text('item_number')->nullable();
         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_medications');
    }
};
