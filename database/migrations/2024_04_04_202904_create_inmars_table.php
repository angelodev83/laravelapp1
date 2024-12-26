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
        Schema::create('inmars', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('po_name')->nullable();
            $table->text('account_number')->nullable();
            $table->text('wholesaler_name')->nullable();
            $table->text('pharmacy_store_id')->nullable();
            $table->integer('status_id')->default(30);
            $table->text('store_document_id')->nullable();
            $table->date('return_date')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmars');
    }
};
