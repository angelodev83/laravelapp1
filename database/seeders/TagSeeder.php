<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::truncate();
        Tag::insert([
            ['code' => 'm_p_dfiqa', 'name' => 'Monthly Pharmacy DFI/QA', 'type' => 'audit'],
            ['code' => 'm_ihs_a_c', 'name' => 'Monthly IHS Audit Checklist', 'type' => 'audit'],
            ['code' => 'm_s_a_qa', 'name' => 'Monthly Self Assessment QA', 'type' => 'audit'],
            ['code' => 'pbm_audit', 'name' => 'PBM Audit', 'type' => 'pbm_audit'],
            ['code' => 'sop', 'name' => 'SOPs', 'type' => 'sop'],
            ['code' => 'pnp', 'name' => 'P&Ps', 'type' => 'sop'],
            ['code' => 'oig_document', 'name' => 'OIG Documents', 'type' => 'oig_document'],
            ['code' => 'ir_monthly_c2', 'name' => 'Control Counts C2', 'type' => 'inventory_reconciliation'],
            ['code' => 'ir_monthly_c3_5', 'name' => 'Control Counts C3 - 5', 'type' => 'inventory_reconciliation'],
            ['code' => 'ir_daily', 'name' => 'Daily Inventory Evaluation', 'type' => 'inventory_reconciliation'],
            ['code' => 'ir_weekly', 'name' => 'Inventory Audit (Weekly)', 'type' => 'inventory_reconciliation'],
            ['code' => 'ctobopsif_monthly', 'name' => 'Complete the Oregon Board of Pharmacy Self Inspection form (Monthly)', 'type' => 'inspection_form'],
        ]);
    }
}
