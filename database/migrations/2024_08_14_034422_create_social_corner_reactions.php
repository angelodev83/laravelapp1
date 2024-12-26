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
        Schema::create('social_corner_reactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('social_corner_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('reaction')->default('heart')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_corner_reactions');
    }
};
