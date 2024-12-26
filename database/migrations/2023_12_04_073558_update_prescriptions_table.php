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
        Schema::table('prescriptions', function (Blueprint $table) {
           
            $table->integer('order_number')->nullable()->after('patient_id');
            $table->string('request_type')->nullable()->after('order_number');
            $table->boolean('telemed_bridge')->nullable()->after('request_type');
            $table->string('prescriber_name')->nullable()->after('telemed_bridge');
            $table->string('prescriber_phone')->nullable()->after('prescriber_name');
            $table->string('prescriber_fax')->nullable()->after('prescriber_phone');
            $table->string('npi')->nullable()->after('prescriber_fax');
            $table->text('medications')->nullable()->after('npi');
            $table->text('notes')->nullable()->after('medications');
            $table->string('requested_for')->nullable()->after('notes');
            $table->boolean('is_addon_applied')->nullable()->after('requested_for');
            $table->date('upload_date')->nullable()->after('is_addon_applied');
            $table->date('submitted_at')->nullable()->after('upload_date');
            $table->date('sent_at')->nullable()->after('submitted_at');
            $table->date('received_at')->nullable()->after('sent_at');
            $table->string('submitted_by')->nullable()->after('received_at');
            $table->integer('stage')->nullable()->default(1)->after('submitted_by');
            $table->integer('status')->nullable()->default(1)->after('stage');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn([
                'order_number', 'request_type', 'telemed_bridge', 'prescriber_name',
                'prescriber_phone', 'prescriber_fax', 'npi', 'date_of_birth',
                'medications', 'requested_for', 'is_addon_applied', 'submitted_at',
                'submitted_by', 'sent_at', 'received_at', 'stage', 'status'
            ]);
        });
    }
};
