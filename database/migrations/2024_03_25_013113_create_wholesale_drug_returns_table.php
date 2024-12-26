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
        Schema::create('wholesale_drug_returns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('drug_order_id')->index()->unsigned();
            $table->bigInteger('drug_order_item_id')->unsigned();
            $table->integer('dispense_quantity')->default(0);
            $table->date('date_filed')->nullable();
            $table->string('bin')->nullable();
            $table->text('reject_comments')->nullable();
            $table->smallInteger('is_compound')->nullable()->default(0);
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('status_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_drug_returns');
    }
};
