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
            $table->text('upc')->nullable();
            $table->text('item_number')->nullable();
            $table->double('awp_price', 11, 2)->default(0);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn('upc');
            $table->dropColumn('item_number');
            $table->dropColumn('awp_price');
           
            $table->dropUnique('medications_medication_key_unique');
        });
    }
};
