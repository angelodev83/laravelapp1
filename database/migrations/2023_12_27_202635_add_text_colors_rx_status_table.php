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
        Schema::table('r_x_stages', function (Blueprint $table) {
            $table->string('text_color')->default('#000000')->nullable()->after('color');
        });

        Schema::table('r_x_statuses', function (Blueprint $table) {
            $table->string('text_color')->default('#000000')->nullable()->after('color');
        });

        Schema::table('shipment_statuses', function (Blueprint $table) {
            $table->string('text_color')->default('#000000')->nullable()->after('color');
        });

        Schema::table('stages', function (Blueprint $table) {
            $table->string('text_color')->default('#000000')->nullable()->after('color');
        });

        Schema::table('statuses', function (Blueprint $table) {
            $table->string('text_color')->default('#000000')->nullable()->after('color');
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('r_x_stages', function (Blueprint $table) {
            $table->dropColumn('text_color');
        });

        Schema::table('r_x_statuses', function (Blueprint $table) {
            $table->dropColumn('text_color');
        });

        Schema::table('shipment_statuses', function (Blueprint $table) {
            $table->dropColumn('text_color');
        });

        Schema::table('stages', function (Blueprint $table) {
            $table->dropColumn('text_color');
        });

        Schema::table('statuses', function (Blueprint $table) {
            $table->dropColumn('text_color');
        });
    }
};
