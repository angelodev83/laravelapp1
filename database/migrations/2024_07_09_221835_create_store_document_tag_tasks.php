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
        Schema::create('store_document_tag_tasks', function (Blueprint $table) {
            $table->bigInteger('store_document_tag_id')->index()->unsigned();
            $table->bigInteger('task_id')->index()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_document_tag_tasks');
    }
};
