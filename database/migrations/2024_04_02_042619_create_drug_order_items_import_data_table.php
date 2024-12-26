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
        $this->down();
        Schema::create('drug_order_items_import_data', function (Blueprint $table) {
            $table->id();
            $table->text('abc_number')->nullable();
            $table->text('product_description')->nullable();
            $table->string('ndc')->nullable();
            $table->string('abc_selling_uom')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('asp_limit_per_bu')->nullable();
            $table->string('awp')->nullable();
            $table->string('cost_per_mbu')->nullable();
            $table->string('current_acq_cost')->nullable();
            $table->string('dea_class')->nullable();
            $table->string('dea_class_desc')->nullable();
            $table->string('fdb_case_pack')->nullable();
            $table->string('fdb_package_size_qty')->nullable();
            $table->string('hcpcs_code')->nullable();
            $table->string('inner_ndc')->nullable();
            $table->string('mbu')->nullable();
            $table->string('primary_ingredient_hic4_code')->nullable();
            $table->string('primary_ingredient_hic4_desc')->nullable();
            $table->string('product_group')->nullable();
            $table->string('product_group_desc')->nullable();
            $table->string('product_note')->nullable();
            $table->string('route_code')->nullable();
            $table->string('route_desc')->nullable();
            $table->string('sold_by')->nullable();
            $table->string('sold_by_description')->nullable();
            $table->string('supplier_number')->nullable();
            $table->text('supplier_name')->nullable();
            $table->string('unit_size_code')->nullable();
            $table->string('unit_size_qty')->nullable();
            $table->string('unit_strength_qty')->nullable();
            $table->text('upc_barcode')->nullable();
            $table->text('unknown_text')->nullable();
            // 32 columns
            $table->text('run_date_text')->nullable();
            $table->string('path')->nullable();
            $table->bigInteger('store_document_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('drug_order_id')->index()->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_order_items_import_data');
    }
};
