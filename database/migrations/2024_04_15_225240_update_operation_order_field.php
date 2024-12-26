<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\OperationOrder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        OperationOrder::truncate();
        Schema::table('operation_orders', function (Blueprint $table) {
            $table->date('dob')->change();
            $table->bigInteger('import_excel_file_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_orders', function (Blueprint $table) {
            $table->dropColumn('import_excel_file_id');
        });
    }
};
