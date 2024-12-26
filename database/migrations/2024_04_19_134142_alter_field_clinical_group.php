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
        if (!Schema::hasColumn('monthly_clinical_reports', 'pharmacy_store_id')) {
            Schema::table('monthly_clinical_reports', function (Blueprint $table) {
                $table->renameColumn('store_id', 'pharmacy_store_id');
            });
        }

        if (!Schema::hasColumn('patients', 'pharmacy_store_id')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->renameColumn('store_id', 'pharmacy_store_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
