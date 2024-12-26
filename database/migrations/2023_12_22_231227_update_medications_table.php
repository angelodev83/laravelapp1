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
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn('stock_size');
            $table->integer('package_size')->nullable()->after('ndc');
            $table->integer('balance_on_hand')->nullable()->after('package_size');
            $table->string('therapeutic_class')->nullable()->after('balance_on_hand');
            $table->date('last_update_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->string('stock_size')->nullable();
            $table->dropColumn('package_size');
            $table->dropColumn('balance_on_hand');
            $table->dropColumn('therapeutic_class');
            $table->dropColumn('last_update_date');
        });
    }
};
