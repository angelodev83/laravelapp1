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
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['stage', 'status']);

            $table->integer('stage_id')->default(1);
            $table->integer('status_id')->default(1);

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['stage_id', 'status_id']);

            $table->integer('stage')->default(1);
            $table->integer('status')->default(1);
        });
    }
    
};
