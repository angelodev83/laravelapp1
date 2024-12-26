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
        Schema::create('clinic_order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('clinic_order_id');
            $table->text('drug_id');
            $table->text('drugname');
            $table->bigInteger('quantity')->nullable();
            $table->text('ndc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_order_items');
    }
};
