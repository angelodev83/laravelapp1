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
        if (!Schema::hasColumn('news_and_events', 'url')) {
            Schema::table('news_and_events', function (Blueprint $table) {
                $table->text('url')->nullable();
            });
        }
        if (!Schema::hasColumn('news_and_events', 'user_id')) {
            Schema::table('news_and_events', function (Blueprint $table) {
                $table->integer('user_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
