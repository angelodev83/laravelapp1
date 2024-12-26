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
        Schema::table('shipment_statuses', function (Blueprint $table) {
            $table->string('class')->nullable()->after('text_color');
            $table->string('widget_icon')->nullable()->after('class');
            $table->integer('sort')->nullable()->after('widget_icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_statuses', function (Blueprint $table) {
            $table->dropColumn('class');
            $table->dropColumn('widget_icon');
            $table->dropColumn('integer');
        });
    }
};
