<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use File;

class CleanTablesSeeder extends Seeder
{
    protected $toTruncate = [
        'announcements',
        'store_announcements',
        'store_documents',
        'compliance_documents',
        'clinical_orders',
        'document_tags',
        'drug_order_items_import_data',
        'drug_orders',
        'histories',
        'imported_excels',
        'inventory_reconciliation_documents',
        // 'items',
        'notifications',
        'operation_orders',
        'operation_returns',
        // 'orders',
        'pharmacy_operations',
        'pharmacy_prescriptions',
        'pharmacy_staff',
        'prescriptions',
        'return_items',
        'supply_order_items',
        'supply_orders',
        'tasks',
        'tickets',
        'wholesale_drug_return_items',
        'wholesale_drug_returns',
        'task_tag'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();

        foreach($this->toTruncate as $table) {
            DB::table($table)->truncate();
        }

        File::deleteDirectory(public_path('/upload/stores'));

        $this->call(PharmacyStoreSeeder::class);

        Schema::enableForeignKeyConstraints();

        Model::reguard();
    }
}
