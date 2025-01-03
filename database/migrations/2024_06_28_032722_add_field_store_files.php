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
        Schema::table('store_files', function (Blueprint $table) {
            $table->string("background_color")->nullable();
            $table->string("text_color")->nullable();
            $table->string("border_color")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_files', function (Blueprint $table) {
            //
        });
    }
};
