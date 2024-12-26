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
        Schema::create('transfer_tasks', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('description');
            $table->text('color');
            $table->text('text_color');
            $table->text('class');
            $table->text('widget_icon');
            $table->integer('sort');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_tasks');
    }
};
