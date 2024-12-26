<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('news_and_events', 'pharmacy_store_id')) {
            Schema::table('news_and_events', function (Blueprint $table) {
                $table->integer('pharmacy_store_id')->nullable();
            });
        }
        DB::statement('ALTER TABLE news_and_events CHANGE `store_statuses_id` status_id integer DEFAULT NULL');
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
