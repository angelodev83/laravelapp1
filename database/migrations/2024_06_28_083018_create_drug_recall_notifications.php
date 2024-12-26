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
        Schema::create('drug_recall_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable();
            $table->date('notice_date')->nullable();
            $table->bigInteger('wholesaler_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->text('comments')->nullable();
            $table->bigInteger('pharmacy_store_id')->index()->nullable();
            $table->bigInteger('user_id')->index()->nullable();
            $table->timestamps();
        });

        Schema::create('drug_recall_notification_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('drug_recall_notification_id')->index()->nullable();
            $table->string('med_id')->nullable();
            $table->string('drug_name')->nullable();
            $table->string('lot_number')->nullable();
            $table->string('ndc')->nullable();
            $table->date('expiration_date')->nullable();
            $table->integer('qty')->nullable();
            $table->bigInteger('user_id')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_recall_notifications');
        Schema::dropIfExists('drug_recall_notification_items');
    }
};
