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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['rx_image']);
            $table->dropColumn('rx_image');

            $table->dropForeign(['intake_form']);
            $table->dropColumn('intake_form');

            $table->dropForeign(['pod_proof_of_delivery']);
            $table->dropColumn('pod_proof_of_delivery');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('rx_image')->nullable();
            $table->integer('intake_form')->nullable();
            $table->integer('pod_proof_of_delivery')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('rx_image');
            $table->dropColumn('intake_form');
            $table->dropColumn('pod_proof_of_delivery');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('rx_image')->nullable();
            $table->foreign('rx_image')->references('id')->on('files')->where('document_type', 'rx_image');

            $table->unsignedBigInteger('intake_form')->nullable();
            $table->foreign('intake_form')->references('id')->on('files')->where('document_type', 'intake_form');

            $table->unsignedBigInteger('pod_proof_of_delivery')->nullable();
            $table->foreign('pod_proof_of_delivery')->references('id')->on('files')->where('document_type', 'pod');
        });
    }
};
