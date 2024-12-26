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
        Schema::create('clinical_renewal_comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment')->nullable();
            $table->bigInteger('clinical_renewal_id')->index();
            $table->bigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('clinical_renewal_comment_documents', function (Blueprint $table) {
            $table->bigInteger('clinical_renewal_comment_id');
            $table->bigInteger('document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_renewal_comments');
        Schema::dropIfExists('clinical_renewal_comment_documents');
    }
};
