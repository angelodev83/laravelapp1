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
        Schema::create('store_document_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tag_id')->index()->unsigned();
            $table->bigInteger('pharmacy_store_id')->index()->unsigned()->nullable();
            $table->smallInteger('day')->nullable();
            $table->smallInteger('month')->index()->nullable();
            $table->integer('year')->index()->nullable();
            $table->integer('week')->nullable();
            $table->smallInteger('month_week')->index()->nullable();
            $table->string('custom_name')->nullable();
            $table->text('custom_data')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_document_tags');
    }
};
