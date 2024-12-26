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
            $table->string('name')->after('id')->nullable();
            $table->string('mime_type')->nullable();
            $table->double('size')->nullable();
            $table->string('size_type')->nullable();
            $table->datetime('last_modified')->nullable();
            $table->smallInteger('s3_bucket')->default(1)->nullable();
            $table->text('url')->nullable();
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
