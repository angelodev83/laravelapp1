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
        Schema::table('store_documents', function (Blueprint $table) {
            $table->string("icon_path")->nullable()->default('/source-images/knowledge-base/Default.png');
            $table->string("background_color")->nullable()->default('#fcd0b2');
            $table->string("text_color")->nullable()->default('black');
            $table->string("border_color")->nullable()->default('#fcd0b2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_documents', function (Blueprint $table) {
            //
        });
    }
};
