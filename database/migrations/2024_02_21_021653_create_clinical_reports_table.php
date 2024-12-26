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
        Schema::create('clinicals', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->integer('sort')->nullable();
            $table->text('data_type')->nullable(); //integer or percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinicals');
    }
};
