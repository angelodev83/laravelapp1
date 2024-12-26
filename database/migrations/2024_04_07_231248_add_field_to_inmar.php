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
        if (Schema::hasColumn('inmars', 'user_id')) { } else {
            Schema::table('inmars', function (Blueprint $table) {
                $table->bigInteger('user_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inmars', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
