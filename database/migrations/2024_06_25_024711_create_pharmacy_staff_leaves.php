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
        Schema::create('pharmacy_staff_leaves', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("pharmacy_staff_id")->index();
            $table->bigInteger("user_id")->index();
            $table->bigInteger("updated_by")->nullable();
            $table->date("date_from")->nullable();
            $table->date("date_to")->nullable();
            $table->bigInteger("status_id")->default(901);
            $table->string("type")->default("Unpaid Leave");
            $table->text("reason")->nullable();
            $table->text("reason_for_rejection")->nullable();
            $table->smallInteger("is_select_half_days")->default(0);
            $table->text("half_days_breakdown")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_staff_leaves');
    }
};
