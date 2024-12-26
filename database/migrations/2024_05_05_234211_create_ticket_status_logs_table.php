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
        $this->down();
        Schema::create('ticket_status_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id')->index();
            $table->integer('status_id')->index();
            $table->dateTime('time_start');
            $table->dateTime('time_end')->nullable();
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_status');
        Schema::dropIfExists('ticket_documents');
    }
};
