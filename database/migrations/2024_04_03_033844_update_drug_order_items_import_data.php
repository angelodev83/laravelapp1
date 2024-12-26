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
            $table->string('po_number')->nullable();
            $table->string('ndc')->nullable();
            $table->text('product_description')->nullable();
            $table->string('abc_selling_size')->nullable();
            $table->string('drug_form_pack_size')->nullable();
            $table->string('quantity_ordered')->nullable();
            $table->string('quantity_confirmed')->nullable();
            $table->string('quantity_shipped')->nullable();
            $table->string('prevent_substitution_indicator')->nullable();
            $table->string('shc_code')->nullable();
            $table->string('department_code')->nullable();
            $table->string('gl_code')->nullable();
            $table->string('contract_number')->nullable();
            $table->string('acq_cost')->nullable();
            $table->string('awp')->nullable();
            $table->string('retail_price')->nullable();
            $table->string('retail_price_override')->nullable();
            $table->string('temp_retail_price_override_indicator')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_date')->nullable();
            // 20 columns
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
        Schema::dropIfExists('view_drug_order_items');
    }
};
