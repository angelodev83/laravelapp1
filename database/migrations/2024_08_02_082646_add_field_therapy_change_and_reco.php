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
        Schema::table('clinical_therapy_change_and_reco', function (Blueprint $table) {
            $table->string('is_switched')->nullable();
            $table->text('pertinent_financial_info')->nullable();
            $table->tinyInteger('mail_sent_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinical_therapy_change_and_reco', function (Blueprint $table) {
            //
        });
    }
};
