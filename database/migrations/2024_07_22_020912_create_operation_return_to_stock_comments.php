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
        Schema::create('operation_rts_comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment')->nullable();
            $table->bigInteger('operation_rts_id')->index();
            $table->bigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('operation_rts_comment_documents', function (Blueprint $table) {
            $table->bigInteger('operation_rts_comment_id');
            $table->bigInteger('document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_return_to_stock_comments');
    }
};
