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
        Schema::create('drug_order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->string('med_id');
            $table->string('ndc')->nullable();
            $table->string('inventory_type')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_order_items');
    }
};
