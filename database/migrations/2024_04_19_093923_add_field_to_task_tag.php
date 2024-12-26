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
        Schema::table('task_tag', function (Blueprint $table) {
            $table->bigInteger('week')->nullable()->after('day');
            $table->string('name')->nullable()->after('id')->default('monthly');
            $table->string('day')->nullable()->change();
            $table->string('month')->nullable()->change();
            $table->string('year')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_tag', function (Blueprint $table) {
            $table->dropColumn('week');
            $table->dropColumn('name');
        });
    }
};
