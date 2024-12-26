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
        Schema::table('clinicals', function (Blueprint $table) {
            $table->decimal('goal', 10, 2)->nullable();
        });
                  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinicals', function (Blueprint $table) {
            $table->dropColumn('goal');
        });
    }
};
