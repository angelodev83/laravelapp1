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
        Schema::create('rc_sms_records', function (Blueprint $table) {
            $table->id();
            $table->text('uri')->nullable();
            $table->bigInteger('rc_id');
            $table->text('to_phonenumber')->nullable();
            $table->text('to_name')->nullable();
            $table->text('to_location')->nullable();
            $table->text('from_phonenumber')->nullable();
            $table->text('from_name')->nullable();
            $table->text('from_location')->nullable();
            $table->text('type')->nullable();
            $table->datetime('creation_time')->nullable();
            $table->text('read_status')->nullable();
            $table->text('priority')->nullable();
            $table->text('attachments_id')->nullable();
            $table->text('direction')->nullable();
            $table->text('availability')->nullable();
            $table->text('subject')->nullable();
            $table->text('message_status')->nullable();
            $table->integer('sms_sending_attempts_count')->nullable();
            $table->text('conversation_id')->nullable();
            $table->text('last_modified_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rc_sms_records');
    }
};
