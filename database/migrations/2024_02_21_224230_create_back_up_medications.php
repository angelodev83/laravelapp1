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
        Schema::create('back_up_medications', function (Blueprint $table) {
            $table->text('med_id');
            $table->string('name')->nullable();
            $table->string('ndc')->nullable();
            $table->text('upc')->nullable();
            $table->text('item_number')->nullable();
            $table->integer('package_size')->nullable();
            $table->integer('balance_on_hand')->nullable();
            $table->string('therapeutic_class')->nullable();
            $table->string('category')->nullable();
            $table->string('manufacturer')->nullable();
            $table->decimal('awp_price', 10, 2)->nullable();
            $table->decimal('rx_price', 10, 2)->nullable(); 
            $table->decimal('340b_price', 10, 2)->nullable();
            $table->timestamp('last_update_date')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('back_up_medications');
    }
};
