<?php

namespace Database\Seeders;

use App\Models\StorePage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StorePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StorePage::truncate();
        StorePage::insert([
            ['id' => 1,'code' => 'bulletin', 'name' => 'Bulletin', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 2,'code' => 'dashboard', 'name' => 'Dashboard', 'parent_id' => 1, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 3,'code' => 'task-reminders', 'name' => 'Task Reminders', 'parent_id' => 1, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 4,'code' => 'announcements', 'name' => 'Announcements', 'parent_id' => 1, 'sidebar_icon' => null, 'sort' => 1],

            ['id' => 5,'code' => 'operations', 'name' => 'Operations', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 6,'code' => 'rts', 'name' => 'Return to Stock', 'parent_id' => 5, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 7,'code' => 'fst', 'name' => 'For Shipping Today', 'parent_id' => 5, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 8,'code' => 'fdt', 'name' => 'For Delivery Today', 'parent_id' => 5, 'sidebar_icon' => null, 'sort' => 1],

            ['id' => 9,'code' => 'clinical', 'name' => 'Clinical', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 10,'code' => 'next-gen-patients', 'name' => 'Next Gen Patients', 'parent_id' => 9, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 11,'code' => 'pioneer-patients', 'name' => 'Pioneer Patients', 'parent_id' => 9, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 12,'code' => 'outcomes', 'name' => 'MTM, Outcomes Reports', 'parent_id' => 9, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 13,'code' => 'aherence-reports', 'name' => 'Adherence Reports', 'parent_id' => 9, 'sidebar_icon' => null, 'sort' => 1],

            ['id' => 14,'code' => 'procurement', 'name' => 'Procurement', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 15,'code' => 'pharmacy', 'name' => 'Pharmacy', 'parent_id' => 14, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 16,'code' => 'drug-orders', 'name' => 'Drug Orders', 'parent_id' => 15, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 17,'code' => 'supply-orders', 'name' => 'Supply Orders', 'parent_id' => 15, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 18,'code' => 'wholesale-drug-returns', 'name' => 'Wholesale Drug Returns', 'parent_id' => 15, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 19,'code' => 'inmar-returns', 'name' => 'INMAR Returns ', 'parent_id' => 15, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 20,'code' => 'clinical-orders', 'name' => 'Clinical Orders', 'parent_id' => 14, 'sidebar_icon' => null, 'sort' => 1],
            
            ['id' => 21,'code' => 'data-insights', 'name' => 'Data Insights', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 22,'code' => 'pharmacy-gross-revenue', 'name' => 'Pharmacy Gross Revenue', 'parent_id' => 21, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 23,'code' => 'payments-overview', 'name' => 'Payments Overview', 'parent_id' => 21, 'sidebar_icon' => null, 'sort' => 1],
            
            ['id' => 24,'code' => 'cnr', 'name' => 'Compliance & Regulatory', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 25,'code' => 'pbm-audit', 'name' => 'PBM Audit', 'parent_id' => 24, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 26,'code' => 'oig-check', 'name' => 'OIG Check', 'parent_id' => 24, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 27,'code' => 'self-audit-documents', 'name' => 'Self-Audit Documents', 'parent_id' => 24, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 28,'code' => 'm-p-dfi-qa', 'name' => 'Monthly Pharmacy DFI/QA', 'parent_id' => 27, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 29,'code' => 'm-ihs-a-c', 'name' => 'Monthly IHS Audit Checklist', 'parent_id' => 27, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 30,'code' => 'm-s-a-qa', 'name' => 'Monthly Self Assessment QA', 'parent_id' => 27, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 31,'code' => 'oig-documents', 'name' => 'OIG Documents', 'parent_id' => 24, 'sidebar_icon' => null, 'sort' => 1],
           
            ['id' => 32,'code' => 'escalation', 'name' => 'Escalation', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 33,'code' => 'tickets', 'name' => 'Tickets', 'parent_id' => 32, 'sidebar_icon' => null, 'sort' => 1],
            
            ['id' => 34,'code' => 'knowledge-base', 'name' => 'Knowledge Base', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 35,'code' => 'sops', 'name' => 'SOPs', 'parent_id' => 34, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 36,'code' => 'pnps', 'name' => 'P&Ps', 'parent_id' => 34, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 37,'code' => 'process-documents', 'name' => 'Process Documents', 'parent_id' => 34, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 38,'code' => 'how-to-guide', 'name' => 'Video Guide', 'parent_id' => 34, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 39,'code' => 'board-of-pharmacy', 'name' => 'Board of Pharmacy', 'parent_id' => 34, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 40,'code' => 'pharmacy-forms', 'name' => 'Pharmacy Forms', 'parent_id' => 34, 'sidebar_icon' => null, 'sort' => 1],
            
            ['id' => 41,'code' => 'eod-register-report', 'name' => 'EOD Register Report', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 42,'code' => 'eod-register', 'name' => 'Register', 'parent_id' => 41, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 43,'code' => 'eod-deposit', 'name' => 'Deposit', 'parent_id' => 41, 'sidebar_icon' => null, 'sort' => 1],
            
            ['id' => 44,'code' => 'inventory-reconciliation', 'name' => 'Inventory Reconciliation', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 45,'code' => 'control-counts', 'name' => 'Control Counts', 'parent_id' => 44, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 46,'code' => 'inventory_reconciliation.monthly.c2', 'name' => 'C2', 'parent_id' => 45, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 47,'code' => 'inventory_reconciliation.monthly.c3_5', 'name' => 'C3 - 5', 'parent_id' => 45, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 48,'code' => 'inventory_reconciliation.daily', 'name' => 'Daily Inventory Evaluation', 'parent_id' => 44, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 49,'code' => 'inventory_reconciliation.weekly', 'name' => 'Inventory Audit (Weekly)', 'parent_id' => 44, 'sidebar_icon' => null, 'sort' => 1],
            
            ['id' => 50,'code' => 'pnc', 'name' => 'People & Culture', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 51,'code' => 'ctclusi-pharmacy', 'name' => 'CTCLUSI Pharmacy', 'parent_id' => 50, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 52,'code' => 'offshore', 'name' => 'Offshore', 'parent_id' => 50, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 53,'code' => 'schedules', 'name' => 'Schedules', 'parent_id' => 50, 'sidebar_icon' => null, 'sort' => 1],

            ['id' => 54,'code' => 'financial-reports', 'name' => 'Financial Reports', 'parent_id' => null, 'sidebar_icon' => null, 'sort' => 1],
            ['id' => 55,'code' => 'pharmacy_gross_revenue', 'name' => 'Pharmacy Gross Revenue', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-file-invoice-dollar me-3', 'sort' => 1],
            ['id' => 56,'code' => 'payments_overview', 'name' => 'Payments Overview', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-receipt me-3', 'sort' => 2],
            ['id' => 57,'code' => 'collected_payments', 'name' => 'Collected Payments', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-hand-holding-dollar me-2', 'sort' => 3],
            ['id' => 58,'code' => 'gross_revenue_and_cogs', 'name' => 'Gross Revenue and Cogs', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-filter-circle-dollar me-2', 'sort' => 4],
            ['id' => 59,'code' => 'account_receivables', 'name' => 'Account Receivables', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-money-bill-1-wave me-2', 'sort' => 5],
            // ['id' => 60,'code' => 'eod_reports', 'name' => 'EOD Reports', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-calendar-check me-3', 'sort' => 1],
            ['id' => 61,'code' => 'scalable_exp_analyzer', 'name' => 'Scalable Exp. Analyzer', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-scale-unbalanced me-2', 'sort' => 7],
            ['id' => 62,'code' => 'cash_flow', 'name' => 'Cash Flow', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-money-bill-transfer me-2', 'sort' => 8],
            ['id' => 63,'code' => 'xero_pl', 'name' => 'Profit and Loss', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-calculator me-3', 'sort' => 9],
            ['id' => 64,'code' => 'payroll_percentage', 'name' => 'Payroll Percentage', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-percent me-3', 'sort' => 10],
            
            
            ['id' => 60,'code' => 'eod_reports', 'name' => 'EOD Reports', 'parent_id' => null, 'sidebar_icon' => 'fa-solid fa-calendar-check me-3', 'sort' => 1],
            ['id' => 65,'code' => 'eod_reports_nuc01', 'name' => 'TRP-NUC01', 'parent_id' => 60, 'sidebar_icon' => 'fa-solid fa-cash-register me-2', 'sort' => 1],
            ['id' => 66,'code' => 'eod_reports_nuc05', 'name' => 'TRP-NUC05', 'parent_id' => 60, 'sidebar_icon' => 'fa-solid fa-cash-register me-2', 'sort' => 1],
            ['id' => 67,'code' => 'eod_reports_nuc06', 'name' => 'TRP-NUC06', 'parent_id' => 60, 'sidebar_icon' => 'fa-solid fa-cash-register me-2', 'sort' => 1],

            ['id' => 68,'code' => 'transaction_receipts', 'name' => 'Transaction Receipts', 'parent_id' => null, 'sidebar_icon' => 'fa-solid fa-laptop-file me-3', 'sort' => 1],
            ['id' => 69,'code' => 'transaction_receipts_nuc01', 'name' => 'TRP-NUC01', 'parent_id' => 68, 'sidebar_icon' => 'fa-solid fa-cash-register me-2', 'sort' => 1],
            ['id' => 70,'code' => 'transaction_receipts_nuc05', 'name' => 'TRP-NUC05', 'parent_id' => 68, 'sidebar_icon' => 'fa-solid fa-cash-register me-2', 'sort' => 1],
            ['id' => 71,'code' => 'transaction_receipts_nuc06', 'name' => 'TRP-NUC06', 'parent_id' => 68, 'sidebar_icon' => 'fa-solid fa-cash-register me-2', 'sort' => 1],

            ['id' => 72,'code' => 'remittance_advice', 'name' => 'Remittance Advice', 'parent_id' => 54, 'sidebar_icon' => 'fa-solid fa-money-check-dollar me-2', 'sort' => 6],

            ['id' => 73,'code' => 'accounting_and_finance', 'name' => 'Accounting and Finance', 'parent_id' => null, 'sidebar_icon' => 'fa-solid fa-calculator me-3', 'sort' => 1],
            ['id' => 74,'code' => 'proforma_and_budget', 'name' => 'Proforma and Budget', 'parent_id' => 73, 'sidebar_icon' => 'fa-solid fa-sack-dollar me-2', 'sort' => 2],
            ['id' => 75,'code' => 'weekly_financial_snapshots', 'name' => 'Weekly Financial Snapshots', 'parent_id' => 73, 'sidebar_icon' => 'fa-solid fa-calendar-week me-2', 'sort' => 3],
            ['id' => 76,'code' => 'monthly_income_statement', 'name' => 'Monthly Income Statement', 'parent_id' => 73, 'sidebar_icon' => 'fa-solid fa-calendar-check me-2', 'sort' => 4],
            ['id' => 77,'code' => 'payroll_percentage', 'name' => 'Payroll Percentage', 'parent_id' => 73, 'sidebar_icon' => 'fa-solid fa-percent me-2', 'sort' => 5],

            ['id' => 78,'code' => 'decks', 'name' => 'Decks', 'parent_id' => null, 'sidebar_icon' => 'fa-solid fa-panorama me-3', 'sort' => 1],

            ['id' => 79,'code' => 'meetings', 'name' => 'Meetings', 'parent_id' => 5, 'sidebar_icon' => 'fa-solid fa-notes-medical me-3', 'sort' => 1],

            ['id' => 80,'code' => 'employee_reviews', 'name' => 'Employee Reviews', 'parent_id' => null, 'sidebar_icon' => 'fa-solid fa-notes-medical me-3', 'sort' => 1],

            ['id' => 81,'code' => 'meetings', 'name' => 'Meetings', 'parent_id' => 9, 'sidebar_icon' => 'fa-solid fa-notes-medical me-3', 'sort' => 1],

            ['id' => 82,'code' => 'scalable_analyzer', 'name' => 'Scalable Analyzer', 'parent_id' => 73, 'sidebar_icon' => 'fa-solid fa-scale-unbalanced me-2', 'sort' => 6],
            ['id' => 83,'code' => 'cash_flow_statement', 'name' => 'Cash Flow Statement', 'parent_id' => 73, 'sidebar_icon' => 'fa-solid fa-money-bill-transfer me-2', 'sort' => 7],
            ['id' => 84,'code' => 'process_document', 'name' => 'Process Document (Scribe)', 'parent_id' => 73, 'sidebar_icon' => 'fa-solid fa-file-signature me-2', 'sort' => 8],

            ['id' => 85,'code' => 'references', 'name' => 'References', 'parent_id' => null, 'sidebar_icon' => 'fa-regular fa-folder-open me-3', 'sort' => 1],
            

            ['id' => 86,'code' => 'human_resource', 'name' => 'Human Resource', 'parent_id' => null, 'sidebar_icon' => 'fa fa-folder-closed me-3', 'sort' => 1],
            ['id' => 87,'code' => 'hr_hub', 'name' => 'HR Hub', 'parent_id' => 86, 'sidebar_icon' => 'fa fa-folder-closed me-3', 'sort' => 2],
            ['id' => 88,'code' => 'employees', 'name' => 'Employees', 'parent_id' => 86, 'sidebar_icon' => 'fa fa-folder-closed me-3', 'sort' => 3],
            ['id' => 89,'code' => 'employee_reviews', 'name' => 'Employee Reviews', 'parent_id' => 86, 'sidebar_icon' => 'fa fa-folder-closed me-3', 'sort' => 4],
            ['id' => 90,'code' => 'file_manager', 'name' => 'File Manager', 'parent_id' => null, 'sidebar_icon' => 'fa fa-folder-closed me-3', 'sort' => 5],
        ]);
    }
}
