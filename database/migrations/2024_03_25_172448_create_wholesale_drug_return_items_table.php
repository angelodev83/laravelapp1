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
        Schema::create('wholesale_drug_return_items', function (Blueprint $table) {
            $table->id();
            $table->integer('dispense_quantity')->default(0);
            $table->string('med_id');
            $table->string('ndc')->nullable();
            $table->string('inventory_type')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_drug_return_items');
    }
};
