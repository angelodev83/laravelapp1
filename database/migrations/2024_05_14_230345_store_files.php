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
        Schema::create('store_pages', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->bigInteger('parent_id')->index()->nullable();
            $table->text('url')->nullable();
            $table->string('sidebar_icon')->nullable();
            $table->string('general_class')->nullable();
            $table->string('custom_icon')->nullable();
            $table->integer('sort')->nullable();
            $table->integer('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('store_folders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('page_id')->index();
            $table->string('name');
            $table->text('path')->nullable();
            $table->bigInteger('parent_id')->index()->nullable();
            $table->bigInteger('user_id')->index();
            $table->integer('sort')->nullable();
            $table->timestamps();
        });

        Schema::create('store_files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('folder_id')->index()->nullable();
            $table->string('name');
            $table->text('path');
            $table->string('mime_type')->nullable();
            $table->string('ext')->nullable();
            $table->double('size')->nullable();
            $table->string('size_type')->nullable();
            $table->datetime('last_modified')->nullable();
            $table->smallInteger('s3_bucket')->default(1)->nullable();
            $table->bigInteger('user_id')->index();
            $table->bigInteger('pharmacy_store_id')->index()->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
