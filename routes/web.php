<?php

use App\Http\Controllers\AccountingAccountsPayableController;
use App\Http\Controllers\AccountingAndFinance\DocumentController as AccountingAndFinanceDocumentController;
use App\Http\Controllers\AccountingArAgingController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AccountingPartnershipReconcillationController;
use App\Http\Controllers\AccountingPayrollPercentageController;
use App\Http\Controllers\AccountingProfitabilityController;
use App\Http\Controllers\AccountingSalesMonitoringController;
use App\Http\Controllers\AiChatBoxController;
use App\Http\Controllers\API\RingCentralController;
use App\Http\Controllers\ComplianceAndRegulatoryAuditsController;
use App\Http\Controllers\ComplianceAndRegulatoryBopController;
use App\Http\Controllers\ComplianceAndRegulatoryLicensureController;
use App\Http\Controllers\ComplianceAndRegulatoryProviderManualsController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\CURL\TebraController;
use App\Http\Controllers\CURL\ZenefitsController;
use App\Http\Controllers\CustomerSupportMarketingController;
use App\Http\Controllers\CustomerSupportSalesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


//App Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionFourMarketingController;
use App\Http\Controllers\DivisionFourSalesController;
use App\Http\Controllers\DivisionOneController;
use App\Http\Controllers\DivisionThreeDivisinTwoAController;
use App\Http\Controllers\DivisionThreeDivisinTwoBController;
use App\Http\Controllers\DivisionThreeDivisionOneTelebridgeController;
use App\Http\Controllers\DivisionThreeTaskController;
use App\Http\Controllers\DivisionTwoAB2BController;
use App\Http\Controllers\DivisionTwoAD2CController;
use App\Http\Controllers\DivisionTwoADataAndReportingController;
use App\Http\Controllers\DivisionTwoAUOFController;
use App\Http\Controllers\DivisionTwoB\PatientController as DivisionTwoBPatientController;
use App\Http\Controllers\DivisionTwoBPharmacyController;
use App\Http\Controllers\DivisionTwoBPharmacySupportController;
use App\Http\Controllers\DivisionTwoBMailOrderController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HumanResourcesEmployeesRelationsController;
use App\Http\Controllers\HumanResourcesRecruitmentAndHiringController;
use App\Http\Controllers\HumanResourcesAnnouncementsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\Oig_exclusion_listController;
use App\Http\Controllers\PrecurementOnlineController;
use App\Http\Controllers\PrecurementRetailController;
use App\Http\Controllers\PrecurementUbcareController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MonthlyClinicalReportController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RbacController;
use App\Http\Controllers\PharmacyStaffController;
use App\Http\Controllers\PharmacyStoreController;
use App\Http\Controllers\PharmacySupportController;
use App\Http\Controllers\PharmacyOperationController;

use App\Http\Controllers\ClinicController;
use App\Http\Controllers\ShipmentStatusController;

use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\EmployeeController;

// CTCLUSI Controllers
use App\Http\Controllers\CTCLUSI\InmarController as CtclusiInmarController;
use App\Http\Controllers\EODRegisterReport\Register;

// Store Controllers
use App\Http\Controllers\PharmacyStoreAdmin\StoreStatusController;
use App\Http\Controllers\PharmacyStoreAdmin\StoreDocumentController;

use App\Http\Controllers\Compliance\ComplianceAuditController;
use App\Http\Controllers\Compliance\ComplianceDocumentController;
use App\Http\Controllers\Compliance\InventoryReconciliationDocumentController;
use App\Http\Controllers\Compliance\OIGCheckController;
use App\Http\Controllers\Compliance\OIGDocumentController;
use App\Http\Controllers\Compliance\SelfAuditDocuments\MonthlyPharmacyDfiqaController;
use App\Http\Controllers\Compliance\SelfAuditDocuments\MonthlyIhsAuditChecklistController;
use App\Http\Controllers\Compliance\SelfAuditDocuments\MonthlySelfAssessmentQaController;
use App\Http\Controllers\Compliance\InventoryReconciliationDocuments\MonthlyControlCountsController;
use App\Http\Controllers\Compliance\InventoryReconciliationDocuments\HighDollarInventoryValuationController;
use App\Http\Controllers\Operations\MailOrderController;
use App\Http\Controllers\Procurement\ClinicalOrderController;
use App\Http\Controllers\Procurement\PharmacySupplyOrderItemController;
use App\Http\Controllers\Procurement\PharmacyDrugOrderItemController;
use App\Http\Controllers\Procurement\PharmacyDrugOrderController;
use App\Http\Controllers\Procurement\PharmacySupplyOrderController;
use App\Http\Controllers\Procurement\PharmacyWholesaleDrugReturnController;
use App\Http\Controllers\Procurement\InmarController;
use App\Http\Controllers\Escalation\TicketController;
use App\Http\Controllers\Escalation\TicketDocumentController;
use App\Http\Controllers\Bulletin\StoreAnnouncementController;
use App\Http\Controllers\Bulletin\TaskController;
use App\Http\Controllers\Bulletin\DashboardController as BulletinDashboardController;
use App\Http\Controllers\Clinical\PioneerPatientController;
use App\Http\Controllers\Clinical\AdherenceReportController;
use App\Http\Controllers\Clinical\BrandSwitchingController;
use App\Http\Controllers\Clinical\BridgedPatientController;
use App\Http\Controllers\Clinical\DashboardController as ClinicalDashboardController;
use App\Http\Controllers\Clinical\KpiController;
use App\Http\Controllers\Clinical\MeetingController as ClinicalMeetingController;
use App\Http\Controllers\Clinical\OutreachController;
use App\Http\Controllers\Clinical\PrioAuthorizationController;
use App\Http\Controllers\Clinical\RenewalCommentController;
use App\Http\Controllers\Clinical\RenewalController;
use App\Http\Controllers\Clinical\RxDailyCensusController;
use App\Http\Controllers\Clinical\RxDailyTransferController;
use App\Http\Controllers\Clinical\PendingRefillRequestController;
use App\Http\Controllers\Clinical\TebraPatientController;
use App\Http\Controllers\Clinical\TherapyChangeAndRecoController;
use App\Http\Controllers\ControlCountController;
use App\Http\Controllers\EODRegisterReport\DepositController;
use App\Http\Controllers\Operations\OperationReturnController;
// use App\Http\Controllers\Operations\MonthlyRevenueController;
use App\Http\Controllers\Operations\OperationOrderController;
use App\Http\Controllers\DataInsights\PharmacyGrossRevenueController;
use App\Http\Controllers\Transfer\TribeMemberController;
use App\Models\OperationReturn;
use App\Http\Controllers\KnowledgeBase\SOPController;
use App\Http\Controllers\KnowledgeBase\PNPController;
use App\Http\Controllers\EODRegisterReport\RegisterController;
use App\Http\Controllers\ExecutiveDashboardController;
use App\Http\Controllers\DataInsights\AccountReceivablesController;
use App\Http\Controllers\DataInsights\CollectedPaymentsController;
use App\Http\Controllers\DataInsights\GrossRevenueAndCogsController;
use App\Http\Controllers\DataInsights\GrossSalesController;
use App\Http\Controllers\DataInsights\PaymentOverviewController;
use App\Http\Controllers\EmployeeReviewsController;
use App\Http\Controllers\Escalation\ReviewController;
use App\Http\Controllers\HumanResource\EmployeeController as StoreEmployeeController;
use App\Http\Controllers\HumanResource\OrganizationController;
use App\Http\Controllers\HumanResource\ScheduleController;
use App\Http\Controllers\InventoryReconciliation\DailyInventoryEvaluationController;
use App\Http\Controllers\InventoryReconciliation\MonthlyControlCountsC2Controller;
use App\Http\Controllers\InventoryReconciliation\MonthlyControlCountsC3To5Controller;
use App\Http\Controllers\InventoryReconciliation\WeeklyInventoryAuditController;
use App\Http\Controllers\KnowledgeBase\AllFilesController;
use App\Http\Controllers\KnowledgeBase\BoardOfPharmacyController;
use App\Http\Controllers\KnowledgeBase\HowToGuideController;
use App\Http\Controllers\KnowledgeBase\PharmacyFormsController;
use App\Http\Controllers\KnowledgeBase\ProcessDocumentsController;
use App\Http\Controllers\Marketing\NewsAndEventsController;
use App\Http\Controllers\Marketing\StoreAnnouncementController as MarketingStoreAnnouncementController;
use App\Http\Controllers\Operations\ForDeliveryTodayController;
use App\Http\Controllers\Procurement\DrugRecallNotificationController;
use App\Http\Controllers\StoreFileController;
use App\Http\Controllers\FinancialReports\DocumentController;
use App\Http\Controllers\HumanResource\LeaveController;
use App\Http\Controllers\HumanResourcesFileManagerController;
use App\Http\Controllers\HumanResourcesHubController;
use App\Http\Controllers\JotForm\PatientPrescriptionTransferController;
use App\Http\Controllers\Marketing\DeckController;
use App\Http\Controllers\Marketing\ReferenceController;
use App\Http\Controllers\Operations\MeetingController;
use App\Http\Controllers\Operations\DashboardController as OperationsDashboardController;
use App\Http\Controllers\Operations\RTSCommentController;
use App\Http\Controllers\Operations\RTSController;
use App\Http\Controllers\PatientIntakeController;
use App\Http\Controllers\Procurement\DrugOrderInvoiceController;
use App\Http\Controllers\Procurement\DrugRecallNotificationItemController;
use App\Http\Controllers\ReleaseOfInformationController;
use App\Http\Controllers\SelfAuditDocumentController;
use App\Http\Controllers\SocialCornerController;
use App\Http\Controllers\StoreFolderController;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/oauth/redirect', function () {
    return Socialite::driver('nextgen')->redirect();
});

Route::get('/oauth/callback', function (Request $request) {
    // Extract the authorization code from the query parameters
    $code = $request->query('code');

    if (!$code) {
        return response()->json(['error' => 'Authorization code not found'], 400);
    }

    // Optionally, retrieve the session_state if needed
    $sessionState = $request->query('session_state');

    // // Continue with processing the code, e.g., exchanging it for an access token
    // $user = Socialite::driver('nextgen')->userFromToken($code);

    // Redirect to a controller method passing the $code variable
    // return app(YourController::class)->handleCallback($code);
    
    // Your logic here, e.g., creating user, setting tokens, etc.
    return redirect('/');
});

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin/logout', [UserController::class, 'destroy'])->name('admin.logout');
    Route::get('/lost', [AuthenticatedSessionController::class, 'lost']);
    
    // DashboardController
    Route::middleware(['permission:executive_dashboard.index'])->get('/admin', [DashboardController::class, 'index']);
    Route::middleware(['permission:executive_dashboard.index'])->get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/admin/addpatient', [DashboardController::class, 'addpatient']);
    Route::get('/admin/files', [DashboardController::class, 'files']);
    Route::get('/admin/prescriptions', [DashboardController::class, 'prescriptions']);
    Route::get('/admin/prescriptions/status/{status_id?}', [DashboardController::class, 'prescriptions']);
    Route::get('/admin/prescriptions/stage/{stage_id?}', [DashboardController::class, 'prescriptions']);

    // MGMT88 AI - AIChatboxController
    Route::get('/admin/chatbox', [AiChatBoxController::class, 'index']);

    // Announcement Unread Notifications
    Route::get('/admin/unread-announcements', [DashboardController::class, 'getAllUnreadNotificationsByUserId']);
    Route::post('/admin/view-announcement', [DashboardController::class, 'showAnnouncement']);


    /**
     * Accounting and Finance
     */
    Route::prefix('admin/accounting-and-finance')->group(function () {
        // Documents
        Route::get('documents/all/{page_id}', [AccountingAndFinanceDocumentController::class, 'all']);
        Route::delete('documents/delete', [AccountingAndFinanceDocumentController::class, 'delete']);
        Route::prefix('documents/{page_id}')->group(function () {
            Route::get('/', [AccountingAndFinanceDocumentController::class, 'index']);
            Route::post('data', [AccountingAndFinanceDocumentController::class, 'data']);
            Route::post('add', [AccountingAndFinanceDocumentController::class, 'store']);
            Route::put('edit', [AccountingAndFinanceDocumentController::class, 'update']);
        });
    });

    // DivisionOneController
    Route::get('/admin/divisionone', [DivisionOneController::class, 'index']);

    // DivisionTwoAUOFController
    Route::get('/admin/divisiontwoa/ubacare_order_fulfillment', [DivisionTwoAUOFController::class, 'index']);

    // DivisionTwoAB2BController
    Route::get('/admin/divisiontwoa/b2b', [DivisionTwoAB2BController::class, 'index']);

    // DivisionTwoAD2CController
    Route::get('/admin/divisiontwoa/d2c', [DivisionTwoAD2CController::class, 'index']);

    // DivisionTwoADataAndReportingController
    Route::get('/admin/divisiontwoa/data_and_reporting', [DivisionTwoADataAndReportingController::class, 'index']);

     //Division2BMailOrderController
     Route::get('/admin/divisiontwob/mail_orders', [DivisionTwoBMailOrderController::class, 'index']);
     Route::post('/admin/divisiontwob/mail_orders/data', [DivisionTwoBMailOrderController::class, 'get_data']);
     Route::post('/admin/divisiontwob/patients/patients_get_data', [DivisionTwoBMailOrderController::class, 'patients_get_data']);
     Route::get('/admin/mail_orders/{id}', [DivisionTwoBMailOrderController::class, 'getOrder']);
     //Renewals
    Route::get('/renewals', [RenewalController::class, 'index'])->name('renewals.index');



    // DivisionTwoBPharmacyController
    Route::get('/admin/divisiontwob/pharmacy', [DivisionTwoBPharmacyController::class, 'index']);
    Route::post('/admin/divisiontwob/get_staff_data', [DivisionTwoBPharmacyController::class, 'get_staff_data']);
    Route::get('/admin/divisiontwob/pharmacy/get_employees', [DivisionTwoBPharmacyController::class, 'get_employees']);
    Route::get('/admin/divisiontwob/pharmacy/get_stores', [DivisionTwoBPharmacyController::class, 'get_stores']);

    // (DivisionTwoB) PharmacyStaffController
    Route::middleware(['permission:pharmacy_staff.create'])->post('/admin/divisiontwob/pharmacy_staff/add_staff', [PharmacyStaffController::class, 'add_staff']);
    Route::middleware(['permission:pharmacy_staff.update'])->post('/admin/divisiontwob/pharmacy_staff/update_staff', [PharmacyStaffController::class, 'update_staff']);
    Route::middleware(['permission:pharmacy_staff.delete'])->post('/admin/divisiontwob/pharmacy_staff/delete_staff', [PharmacyStaffController::class, 'delete_staff']);

    // (DivisionTwoB) PharmacyStoreController
    Route::get('/admin/divisiontwob/pharmacy_store', [PharmacyStoreController::class, 'index']);
    Route::post('/admin/divisiontwob/pharmacy_store/get_data', [PharmacyStoreController::class, 'get_data']);
    Route::middleware(['permission:pharmacy_store.create'])->post('/admin/divisiontwob/pharmacy_store/add_store', [PharmacyStoreController::class, 'add_store']);
    Route::middleware(['permission:pharmacy_store.update'])->post('/admin/divisiontwob/pharmacy_store/update_store', [PharmacyStoreController::class, 'update_store']);
    Route::middleware(['permission:pharmacy_store.delete'])->post('/admin/divisiontwob/pharmacy_store/delete_store', [PharmacyStoreController::class, 'delete_store']);

    // DivisionTwoBPharmacySupportController
    Route::get('/admin/divisiontwob/pharmacy_support', [DivisionTwoBPharmacySupportController::class, 'index']);
    Route::get('/admin/divisiontwob/pharmacy_support/get_employees', [DivisionTwoBPharmacySupportController::class, 'get_employees']);
    Route::get('/admin/divisiontwob/pharmacy_support/get_operations', [DivisionTwoBPharmacySupportController::class, 'get_operations']);

    // (DivisionTwoB) PharmacySupportController
    Route::post('/admin/divisiontwob/pharmacy_support/get_data', [PharmacySupportController::class, 'get_data']);
    Route::post('/admin/divisiontwob/pharmacy_support/add_support', [PharmacySupportController::class, 'add_support']);
    Route::post('/admin/divisiontwob/pharmacy_support/update_support', [PharmacySupportController::class, 'update_support']);
    Route::post('/admin/divisiontwob/pharmacy_support/delete_support', [PharmacySupportController::class, 'delete_support']);

    // (DivisionTwoB) PharmacyOperationController
    Route::get('/admin/divisiontwob/pharmacy_operation', [PharmacyOperationController::class, 'index']);
    Route::post('/admin/divisiontwob/pharmacy_operation/get_data', [PharmacyOperationController::class, 'get_data']);
    Route::middleware(['permission:pharmacy_operation.create'])->post('/admin/divisiontwob/pharmacy_operation/add_operation', [PharmacyOperationController::class, 'add_operation']);
    Route::middleware(['permission:pharmacy_operation.update'])->post('/admin/divisiontwob/pharmacy_operation/update_operation', [PharmacyOperationController::class, 'update_operation']);
    Route::middleware(['permission:pharmacy_operation.delete'])->post('/admin/divisiontwob/pharmacy_operation/delete_operation', [PharmacyOperationController::class, 'delete_operation']);


    // DivisionThreeDivisionOneTelebridgeController
    Route::get('/admin/divisionthree/divisionone_telebridge', [DivisionThreeDivisionOneTelebridgeController::class, 'index']);

    // DivisionThreeDivisionTwoAController
    Route::get('/admin/divisionthree/divisiontwoa', [DivisionThreeDivisinTwoAController::class, 'index']);

    // DivisionThreeDivisionTwoBController
    Route::get('/admin/divisionthree/divisiontwob', [DivisionThreeDivisinTwoBController::class, 'index']);

    // DivisionThreeTaskController
    Route::get('/admin/divisionthree/task', [DivisionThreeTaskController::class, 'index']);
    Route::get('/admin/divisionthree/task/get_dropdown_data', [DivisionThreeTaskController::class, 'get_dropdown_data']);
    Route::get('/admin/divisionthree/task/get_outlier_type', [DivisionThreeTaskController::class, 'get_outlier_type']);
    Route::post('/admin/divisionthree/task/data', [DivisionThreeTaskController::class, 'get_data']);
    Route::post('/admin/divisionthree/task/store', [DivisionThreeTaskController::class, 'store']);
    Route::post('/admin/divisionthree/task/update', [DivisionThreeTaskController::class, 'update']);
    Route::post('/admin/divisionthree/task/delete', [DivisionThreeTaskController::class, 'destroy']);
    Route::post('/admin/divisionthree/task/upload_csv', [DivisionThreeTaskController::class, 'upload_csv']);


    //MonthlyReportController
    Route::get('/admin/division3/monthly_report/{year?}/{store?}', [MonthlyClinicalReportController::class, 'index']);
    Route::get('/admin/monthly_report/get_stores', [MonthlyClinicalReportController::class, 'get_stores']);
    Route::post('/admin/monthly_report/add_report', [MonthlyClinicalReportController::class, 'add_report']);
    Route::post('/admin/monthly_report/update_report', [MonthlyClinicalReportController::class, 'update_report']);

    // DivisionFourSalesController
    Route::get('/admin/divisionfour/sales', [DivisionFourSalesController::class, 'index']);

    // DivisionFourMarketingController
    Route::get('/admin/divisionfour/marketing', [DivisionFourMarketingController::class, 'index']);

    // DivisionFourMarketingController
    Route::get('/admin/divisionfour/marketing', [DivisionFourMarketingController::class, 'index']);

    // CustomerSupportSalesController
    Route::get('/admin/customer_support/sales', [CustomerSupportSalesController::class, 'index']);

    // CustomerSupportMarketingController
    Route::get('/admin/customer_support/marketing', [CustomerSupportMarketingController::class, 'index']);

    // AccountingSalesMonitoringController
    Route::get('/admin/accounting/sales_monitoring', [AccountingSalesMonitoringController::class, 'index']);

    // AccountingPayrollPercentageController
    Route::get('/admin/accounting/payroll_percentage', [AccountingPayrollPercentageController::class, 'index']);

    // AccountingArAgingController
    Route::get('/admin/accounting/ar_aging', [AccountingArAgingController::class, 'index']);

    // AccountingProfitabilityController
    Route::get('/admin/accounting/profitability', [AccountingProfitabilityController::class, 'index']);

    //CollectedPayments
    Route::get('/collected-payments', [CollectedPaymentsController::class, 'index'])->name('collected-payments.index');


    // AccountingPartnershipReconcillationController
    Route::get('/admin/accounting/partnership_reconcillation', [AccountingPartnershipReconcillationController::class, 'index']);

    // AccountingAccountsPayableController
    Route::get('/admin/accounting/accounts_payable', [AccountingAccountsPayableController::class, 'index']);

    // PrecurementUbcareController
    Route::get('/admin/precurement/ubcare', [PrecurementUbcareController::class, 'index']);

    // PrecurementOnlineController
    Route::get('/admin/precurement/online', [PrecurementOnlineController::class, 'index']);

    // PrecurementRetailController
    Route::get('/admin/precurement/retail', [PrecurementRetailController::class, 'index']);

    // Accounting
    Route::get('/admin/accounting', [AccountingController::class, 'index']);

    // HumanResourcesEmployeesRelationsController
    Route::get('/admin/human_resources/employees_relations', [HumanResourcesEmployeesRelationsController::class, 'index']);
    Route::post('/admin/human_resources/data', [HumanResourcesEmployeesRelationsController::class, 'get_data']);
    Route::middleware(['permission:human-resource.employees.create'])->post('/admin/human_resources/add_employee', [HumanResourcesEmployeesRelationsController::class, 'add_employee']);
    Route::middleware(['permission:human-resource.employees.delete'])->post('/admin/human_resources/delete_employee', [HumanResourcesEmployeesRelationsController::class, 'delete_employee']);
    Route::middleware(['permission:human-resource.employees.update'])->post('/admin/human_resources/update_employee', [HumanResourcesEmployeesRelationsController::class, 'update_employee']);
    Route::middleware(['permission:human-resource.employees.import'])->get('/admin/human_resources/populate_csv', [HumanResourcesEmployeesRelationsController::class, 'populate_employee_csv']);

    // Human Resource > Employee Reviews (files)
    Route::get('/admin/human_resources/employee-reviews/{year}/{month_number}', [EmployeeReviewsController::class, 'index']);
    Route::post('/admin/human_resources/employee-reviews/data', [EmployeeReviewsController::class, 'data']);
    Route::middleware(['permission:hr.employee_reviews.create'])
        ->post('/admin/human_resources/employee-reviews/add', [EmployeeReviewsController::class, 'store']);
    Route::middleware(['permission:hr.employee_reviews.delete'])
        ->delete('/admin/human_resources/employee-reviews/delete', [EmployeeReviewsController::class, 'delete']);
    Route::middleware(['permission:hr.employee_reviews.update'])
        ->put('/admin/human_resources/employee-reviews/edit', [EmployeeReviewsController::class, 'update']);
    
    // HumanResourcesRecruitmentAndHiringController
    Route::get('/admin/human_resources/recruitment_and_hiring', [HumanResourcesRecruitmentAndHiringController::class, 'index']);

    // HumanResourcesAnnouncementsController
    Route::get('/admin/human_resources/announcements', [HumanResourcesAnnouncementsController::class, 'index']);
    Route::get('/admin/human_resources/announcements/{id}', [HumanResourcesAnnouncementsController::class, 'show']);
    Route::post('/admin/human_resources/announcements/data', [HumanResourcesAnnouncementsController::class, 'get_data']);
    Route::post('/admin/human_resources/add_announcement', [HumanResourcesAnnouncementsController::class, 'add_announcement']);
    Route::post('/admin/human_resources/update_announcement', [HumanResourcesAnnouncementsController::class, 'update_announcement']);
    Route::post('/admin/human_resources/delete_announcement', [HumanResourcesAnnouncementsController::class, 'delete']);

    // Human Resource > HR Hub
    Route::get('/admin/human_resources/hub', [HumanResourcesHubController::class, 'index']);
    // Social Corner
    Route::post('/admin/social-corner/add', [SocialCornerController::class, 'store']);
    Route::post('/admin/social-corner/load-more', [SocialCornerController::class, 'loadMore']);
    Route::post('/admin/social-corner/react', [SocialCornerController::class, 'react']);

    // Human Resource > File Manager
    Route::prefix('/admin/human_resources/file-manager')->group(function () {
        Route::get('/', [HumanResourcesFileManagerController::class, 'index']);
        Route::post('data', [HumanResourcesFileManagerController::class, 'data']);
        Route::middleware(['permission:hr.file_manager.create'])
            ->post('add', [HumanResourcesFileManagerController::class, 'store']);
        Route::middleware(['permission:hr.file_manager.update'])
            ->post('edit', [HumanResourcesFileManagerController::class, 'update']);
        Route::middleware(['permission:hr.file_manager.delete'])
            ->post('delete', [HumanResourcesFileManagerController::class, 'delete']);
    });

    // ComplianceAndRegulatoryLicensureController
    Route::get('/admin/compliance_and_regulatory/licensure', [ComplianceAndRegulatoryLicensureController::class, 'index']);

    // ComplianceAndRegulatoryAuditsController
    Route::get('/admin/compliance_and_regulatory/audits', [ComplianceAndRegulatoryAuditsController::class, 'index']);

    // ComplianceAndRegulatoryProviderManualsController
    Route::get('/admin/compliance_and_regulatory/provider_manuals', [ComplianceAndRegulatoryProviderManualsController::class, 'index']);

    // ComplianceAndRegulatoryBopController
    Route::get('/admin/compliance_and_regulatory/bop', [ComplianceAndRegulatoryBopController::class, 'index']);


    // FileController
    Route::post('/admin/file/delete_file_via_ajax', [FileController::class, 'delete_file_via_ajax']);
    Route::get('/admin/files/csv_upload', [FileController::class, 'csv_upload']);
    Route::get('/admin/files/upload', [FileController::class, 'upload']);
    Route::post('/admin/upload_csv', [FileController::class, 'upload_csv']);
    Route::post('/admin/csv_uploader', [FileController::class, 'csv_uploader']);
    Route::post('/admin/xlsx_uploader', [FileController::class, 'xlsx_uploader']);
    Route::get('/admin/file/download/{id}', [FileController::class, 'download']);

    // ItemController
    Route::post('/admin/update/rxStage/{id}', [ItemController::class, 'updateRXStage']);
    Route::post('/admin/update/rxStatus/{id}', [ItemController::class, 'updateRXStatus']);
    Route::post('/admin/update/shipmentStatus/{id}', [ItemController::class, 'updateShipmentStatus']);
    Route::post('/admin/item/update', [ItemController::class, 'update']);
    Route::post('/admin/item/delete_item_via_ajax', [ItemController::class, 'delete_item_via_ajax']);
    Route::post('/admin/update/shipmentStatus/{id}', [ItemController::class, 'updateShipmentStatus']);
    Route::post('/admin/item_row/update', [ItemController::class, 'update_item_row']);
    Route::post('/admin/item_row/SaveNewItemRow', [ItemController::class, 'SaveNewItemRow']);

    // MedicationController
    Route::get('/admin/medications', [MedicationController::class, 'index']);
    Route::get('/admin/medications/data', [MedicationController::class,'data']);
    Route::post('/admin/medications/data', [MedicationController::class,'get_data']);
    Route::post('/admin/medications/suggest', [MedicationController::class, 'suggest']);
    Route::post('/admin/medications/getNames', [MedicationController::class, 'getNames']);

    // OrderController
    Route::get('/admin/orders/data', [OrderController::class,'data']);
    Route::get('/admin/orders', [OrderController::class, 'index']);
    Route::get('/admin/orders/division-three', [OrderController::class, 'index'])->name('division_three');
    Route::get('/admin/orders/{id}', [OrderController::class,'getOrder']);
    Route::post('/admin/order/delete_order_via_ajax', [OrderController::class, 'delete_order_via_ajax']);    
    Route::post('/admin/order/update', [OrderController::class, 'update']);

    
  
    // PatientController
    Route::get('/admin/patient/{id}', [PatientController::class, 'viewpatient']);
    Route::get('/admin/patients', [PatientController::class, 'index']);
    Route::get('/admin/patients/data', [PatientController::class,'data']);
    Route::post('/admin/patients/add_patient_via_ajax', [PatientController::class, 'add_patient_via_ajax']);
    Route::post('/admin/patients/delete_patient_via_ajax', [PatientController::class, 'delete_patient_via_ajax']);
    Route::post('/admin/patients/delete_patients_via_ajax', [PatientController::class, 'delete_patients_via_ajax']);
    Route::post('/admin/patients/edit_patient_via_ajax', [PatientController::class, 'edit_patient_via_ajax']);
    Route::post('/admin/patients/search', [PatientController::class, 'search']);
    Route::post('/admin/patient/add_order_via_ajax', [PatientController::class, 'add_order_via_ajax']);
    Route::post('/admin/patient/update_order_via_ajax', [PatientController::class, 'update_order_via_ajax']);
    Route::post('/admin/patient/getNames', [PatientController::class, 'getNames']);

    // PrescriptionController
    Route::post('/admin/patient/add_prescription_via_ajax', [PrescriptionController::class, 'add_prescription_via_ajax']);
    Route::post('/admin/patient/edit_prescription_via_ajax', [PrescriptionController::class, 'edit_prescription_via_ajax']);
    Route::get('/admin/prescriptions/data', [PrescriptionController::class,'data']);
    Route::post('/admin/prescription/delete_prescription_via_ajax', [PrescriptionController::class, 'delete_prescription_via_ajax']);
    Route::post('/admin/prescription/delete_prescriptions_via_ajax', [PrescriptionController::class, 'delete_prescriptions_via_ajax']);    
    Route::get('/export-prescriptions', [PrescriptionController::class, 'exportPrescriptionsToCSV']);
    Route::post('/admin/upload', [PrescriptionController::class, 'upload']);

    // ProfileController
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // UserController
    Route::get('/admin/user/generatekey', [UserController::class, 'generatekey']);

    //Oig_exclusion_listController
    Route::get('/admin/oig_list', [Oig_exclusion_listController::class, 'index']);
    Route::post('/admin/oig_list/data', [Oig_exclusion_listController::class, 'get_data']);
    Route::get('/admin/oig_list/update_oig', [Oig_exclusion_listController::class, 'downloadOigCsv']);
    Route::get('/admin/oig_list/update_offline', [Oig_exclusion_listController::class, 'reloadOigCsv']);

    // ComplianceController
    Route::get('/admin/oig_check', [ComplianceController::class, 'index']);
    Route::post('/admin/oig_check/data', [ComplianceController::class, 'get_data']);
    Route::post('/admin/oig_check/add_employee', [ComplianceController::class, 'add_employee']);
    Route::post('/admin/oig_check/delete_employee', [ComplianceController::class, 'delete_employee']);
    Route::post('/admin/oig_check/update_employee', [ComplianceController::class, 'update_employee']);

    //ZenefitsController
    Route::get('/admin/zenefits/sync', [ZenefitsController::class, 'fetchApiData']);
    Route::get('/admin/zenefits/sync/{id}', [ZenefitsController::class, 'fetchApiData']);
    
    //UserController
    Route::post('/admin/user/get_roles', [UserController::class, 'get_roles']);
    Route::get('/admin/user/get_employees', [UserController::class, 'get_employees']);
    Route::get('/admin/user/get_active_employees', [UserController::class, 'get_active_employees']);
    Route::get('/admin/profile/profile_view', [UserController::class, 'profile_view']);
    Route::get('/admin/profile/edit_profile_view', [UserController::class, 'editProfile_view']);
    Route::put('/admin/profile/update_profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/admin/profile/update_avatar', [UserController::class, 'update_avatar']);
    
    
    // For System Settings - CRUD for USERS
    Route::get('/admin/user', [UserSettingController::class, 'index']);
    Route::post('/admin/user/data', [UserSettingController::class, 'get_data']);
    Route::middleware(['permission:user.create'])->post('/admin/user/add_user', [UserSettingController::class, 'add_user']);
    Route::middleware(['permission:user.update'])->post('/admin/user/update_user', [UserSettingController::class, 'update_user']);
    Route::middleware(['permission:user.delete'])->post('/admin/user/delete_user', [UserSettingController::class, 'delete_user']);
    Route::get('/admin/user/security', [UserController::class, 'security_profile']);
    Route::put('/admin/user/update_password', [UserController::class, 'update_password']);
    

    //RoleController
    Route::get('/admin/role', [RoleController::class, 'index']);
    Route::post('/admin/role/data', [RoleController::class, 'get_data']);
    Route::post('/admin/role/delete_role', [RoleController::class, 'delete_role']);
    Route::post('/admin/role/add_role', [RoleController::class, 'add_role']);
    Route::post('/admin/role/update_role', [RoleController::class, 'update_role']);

    //PermissionController
    Route::post('/admin/permission/data', [PermissionController::class, 'get_data']);

    //RbacController
    Route::get('/admin/rbac', [RbacController::class, 'index']);
    Route::post('/admin/rbac/data', [RbacController::class, 'get_data']);
    Route::post('/admin/rbac/get_roles', [RbacController::class, 'get_roles']);
    Route::middleware(['permission:rbac.index'])->post('/admin/rbac/update_permission', [RbacController::class, 'update_permission']);

    //OutcomesController
    Route::get('/admin/telehealth/outcomes', [OutcomeController::class, 'index']);
    Route::post('/admin/telehealth/outcomes/data', [OutcomeController::class, 'get_data']);
    Route::post('/admin/telehealth/outcomes/delete', [OutcomeController::class, 'delete']);

    // ClinicController
    Route::post('/admin/clinics/getNames', [ClinicController::class, 'getNames']);

     // ShipmentStatusController
    Route::post('/admin/shipment-statuses/data', [ShipmentStatusController::class, 'data']);

    Route::post('/admin/pharmacy-staffs/search-names', [PharmacyStaffController::class, 'searchNames']);

    //TribeMemberController
    //Route::get('store/patient-support/transfer_rx/{id}/tribe_members/', [TribeMemberController::class, 'index']);
    Route::get('/admin/transfer_rx/{list_id}/tribe_members', [TribeMemberController::class, 'index']);
    Route::get('/admin/transfer_rx/tribe_members/get_patient_data', [TribeMemberController::class, 'get_patient_data']);
    Route::get('/admin/transfer_rx/tribe_members/get_data', [TribeMemberController::class, 'get_data']);
    Route::get('/admin/transfer_rx/tribe_members/get_default_list', [TribeMemberController::class, 'get_default_list']);
    Route::put('/admin/transfer_rx/tribe_members/update_task', [TribeMemberController::class, 'update_task']);
    Route::put('/admin/transfer_rx/tribe_members/update_clicked_column', [TribeMemberController::class, 'update_clicked_column']);
    Route::post('/admin/transfer_rx/tribe_members/update_default', [TribeMemberController::class, 'update_default']);
    Route::post('/admin/transfer_rx/tribe_members/patient_store', [TribeMemberController::class, 'patient_store']);
    Route::get('/admin/transfer_rx/tribe_members/get_assignee_data', [TribeMemberController::class, 'get_assignee_data']);
    Route::delete('/admin/transfer_rx/tribe_members/delete_assignee', [TribeMemberController::class, 'delete_assignee']);
    Route::put('/admin/transfer_rx/tribe_members/update_assignees', [TribeMemberController::class, 'update_assignees']);

    /**
     * Search for dropdowns
     */
    Route::post('/admin/search/{name}', [SearchController::class, 'search']);
    Route::post('/admin/employee/add', [EmployeeController::class, 'store']);
    
    Route::get('/admin/store-file/download/s3/{id}', [StoreFileController::class, 'downloadS3']);
    Route::get('/admin/store-document/download/s3/{id}', [StoreDocumentController::class, 'downloadS3']);
    Route::delete('/admin/store-document/delete', [StoreDocumentController::class, 'delete']);

    /**
     * Executive Dashboard
     */
    Route::post('/admin/executive-dashboard/data-insights/charts', [ExecutiveDashboardController::class, 'dataInsightsCharts']);
    Route::post('/admin/executive-dashboard/clinical/charts', [ExecutiveDashboardController::class, 'clinicalCharts']);


    Route::post('/admin/pharmacy-staff/schedules/events', [ScheduleController::class, 'events']);
    Route::get('/admin/pharmacy-staff/leaves/{id}', [LeaveController::class, 'leave']);
    Route::middleware(['permission:menu_store.hr.leaves.create'])
        ->post('/admin/pharmacy-staff/leaves/add', [LeaveController::class, 'store']);
    Route::middleware(['permission:menu_store.hr.leaves.update'])
        ->post('/admin/pharmacy-staff/leaves/update', [LeaveController::class, 'update']);

    Route::put('/admin/store-folders/update', [StoreFolderController::class, 'update']);
    Route::delete('/admin/store-folders/delete', [StoreFolderController::class, 'delete']);
    Route::post('/admin/store-files/recent', [StoreFileController::class, 'recentPerPage']);

    /******************
     * Stores
     *****************/

    Route::prefix('store')->group(function () {
        /**
         * <reference models>
         */
        Route::post('/status/search', [StoreStatusController::class, 'search']);

        /**
         * Bulletin
         */
        Route::prefix('bulletin')->group(function () {
            // Announcements (StoreAnnouncement)
            // Route::get('{id}/announcements', [StoreAnnouncementController::class, 'index']);
            // Route::post('announcements/data', [StoreAnnouncementController::class, 'data']);
            // Route::get('announcements/view/{id}', [StoreAnnouncementController::class, 'show']);
            // Route::middleware(['permission:menu_store.bulletin.announcements.create'])->post('{id}/announcement/add', [StoreAnnouncementController::class, 'store']);
            // Route::middleware(['permission:menu_store.bulletin.announcements.update'])->post('{id}/announcement/edit', [StoreAnnouncementController::class, 'update']);
            // Route::middleware(['permission:menu_store.bulletin.announcements.delete'])->post('/announcement/delete', [StoreAnnouncementController::class, 'delete']);

            // Task
            Route::get('task/load/{task_id}', [TaskController::class, 'loadTask']);
            Route::get('{id}/task-reminders', [TaskController::class, 'index']);
            Route::get('{id}/task/show/{tix_id}', [TaskController::class, 'show']);
            Route::post('{id}/task/show/{tix_id}', [TaskController::class, 'show']);
            Route::post('task/data', [TaskController::class, 'data']);
            Route::post('tasks', [TaskController::class, 'filteredData'])->name('tasks.index');
            // Route::post('task/watchers', [TaskController::class, 'watchers']);
            // Route::post('task/watchers/add', [TaskController::class, 'addWatcher']);
            // Route::delete('task/watchers/delete', [TaskController::class, 'deleteWatcher']);
            Route::post('task-documents/data', [StoreDocumentController::class, 'data']);

            Route::prefix('task')->group(function () {
                Route::get('load-attachments/{task_id}', [TaskController::class, 'loadAttachments']);
                Route::get('load-comments/{task_id}', [TaskController::class, 'loadComments']);
                Route::post('store-attachments', [TaskController::class, 'storeAttachments']);
                Route::post('store-comment', [TaskController::class, 'storeComment']);
                Route::patch('update-details', [TaskController::class, 'updateDetails']);
                Route::post('update-attachments', [TaskController::class, 'updateAttachments']);
                Route::post('assignees', [TaskController::class, 'assignees']);
                Route::post('watchers', [TaskController::class, 'watchers']);
                Route::post('watchers/add', [TaskController::class, 'addWatcher']);
                Route::delete('watchers/delete', [TaskController::class, 'deleteWatcher']);
            });

            Route::middleware(['permission:menu_store.bulletin.task_reminders.create'])->group(function () {
                Route::post('{id}/task/add', [TaskController::class, 'store']);
                Route::post('task-documents/add', [StoreDocumentController::class, 'store']);
            });
            Route::middleware(['permission:menu_store.bulletin.task_reminders.update'])->group(function () {
                Route::post('{id}/task/edit', [TaskController::class, 'update']);
            });
            Route::middleware(['permission:menu_store.bulletin.task_reminders.delete'])->group(function () {
                Route::post('task/delete', [TaskController::class, 'delete']);
                Route::put('task/archive', [TaskController::class, 'archive']);
                Route::put('task/unarchive', [TaskController::class, 'unarchive']);
                Route::post('task-documents/delete', [StoreDocumentController::class, 'delete']);
            });

            // Dashboard
            Route::get('{id}/dashboard', [BulletinDashboardController::class, 'index'])->name('store.dashboard.{id}');
            Route::post('dashboard/task-reminders', [BulletinDashboardController::class, 'taskReminders']);
            Route::post('dashboard/announcements', [BulletinDashboardController::class, 'announcements']);
        });

        /**
         * Operations
         */
        Route::prefix('operations')->group(function () {
            // Dashboard
            Route::get('{id}/dashboard', [OperationsDashboardController::class, 'index']);

            // Mail Orders
            Route::get('{id}/mail-orders', [MailOrderController::class, 'index']);
            Route::post('/mail-orders/data', [DivisionTwoBMailOrderController::class, 'get_data']);
            Route::post('{id}/mail-orders/file-upload', [MailOrderController::class, 'file_upload']);
            Route::post('{id}/mail_orders/data', [DivisionTwoBMailOrderController::class, 'get_data']);
            Route::get('{id}/mail_orders/download/{did}', [DivisionTwoBMailOrderController::class, 'download']);
    
            
            // Return to Stock -- v1
            // Route::middleware(['permission:menu_store.operations.rts.index|menu_store.operations.rts.create|menu_store.operations.rts.update|menu_store.operations.rts.delete'])->get('{id}/rts', [OperationReturnController::class, 'index']);
            // Route::get('{id}/rts/data', [OperationReturnController::class, 'get_data']);
            // Route::get('{id}/rts/get_medications', [OperationReturnController::class, 'get_medications']);
            // Route::get('{id}/rts/get_patients', [OperationReturnController::class, 'get_patients']);
            // Route::get('{id}/rts/get_status', [OperationReturnController::class, 'get_status']);
            // Route::middleware(['permission:menu_store.operations.rts.create'])->post('{id}/rts/store', [OperationReturnController::class, 'store']);
            // Route::middleware(['permission:menu_store.operations.rts.update'])->put('{id}/rts/update', [OperationReturnController::class, 'update']);
            // Route::middleware(['permission:menu_store.operations.rts.delete'])->delete('{id}/rts/delete', [OperationReturnController::class, 'destroy']);
            // Return to Stock -- v2 (new process)
            Route::prefix('{id}/rts')->group(function () {
                Route::get('/', [RTSController::class, 'index']);
                Route::get('patient-data/{rts_id}', [RTSController::class, 'patientData']);
                Route::post('filter-board-data', [RTSController::class, 'filterBoardData']);
                Route::middleware(['permission:menu_store.operations.rts.export'])
                    ->get('/export', [RTSController::class, 'export']);
                Route::middleware(['permission:menu_store.operations.rts.create'])
                    ->post('import', [RTSController::class, 'import']);
                Route::middleware(['permission:menu_store.operations.rts.update'])
                    ->patch('update', [RTSController::class, 'update']);
                Route::middleware(['permission:menu_store.operations.rts.delete'])
                    ->delete('delete', [RTSController::class, 'delete']);
                // comment
                Route::post('comment/store', [RTSCommentController::class, 'store']);
            });

            // For Shipping Today
            Route::get('{id}/for-shipping-today', [OperationOrderController::class, 'index']);
            Route::post('for-shipping-today/data', [OperationOrderController::class, 'data']);
            Route::middleware(['permission:menu_store.operations.for_shipping_today.create'])->post('for-shipping-today/add', [OperationOrderController::class, 'store']);
            Route::middleware(['permission:menu_store.operations.for_shipping_today.create'])->post('for-shipping-today/upload', [OperationOrderController::class, 'upload']);
            Route::middleware(['permission:menu_store.operations.for_shipping_today.update'])->post('for-shipping-today/upload/shipping-label/{id}', [OperationOrderController::class, 'uploadShippingLabel']);
            Route::middleware(['permission:menu_store.operations.for_shipping_today.update'])->post('for-shipping-today/bulk-upload/shipping-label', [OperationOrderController::class, 'uploadBulkShippingLabel']);
            Route::middleware(['permission:menu_store.operations.for_shipping_today.update'])->put('for-shipping-today/edit', [OperationOrderController::class, 'update']);
            Route::middleware(['permission:menu_store.operations.for_shipping_today.update'])->delete('for-shipping-today/upload/shipping-label/delete', [OperationOrderController::class, 'deleteShippingLabel']);
            Route::middleware(['permission:menu_store.operations.for_shipping_today.delete'])->post('for-shipping-today/delete', [OperationOrderController::class, 'delete']);
            Route::middleware(['permission:menu_store.operations.for_delivery_today.update'])->post('for-shipping-today/update-completed', [OperationOrderController::class, 'updateCompleted']);

            // For Delivery Today
            Route::get('{id}/for-delivery-today', [ForDeliveryTodayController::class, 'index']);
            Route::post('for-delivery-today/data', [ForDeliveryTodayController::class, 'data']);
            Route::middleware(['permission:menu_store.operations.for_delivery_today.create'])->post('for-delivery-today/add', [ForDeliveryTodayController::class, 'store']);
            Route::middleware(['permission:menu_store.operations.for_delivery_today.create'])->post('for-delivery-today/upload', [ForDeliveryTodayController::class, 'upload']);
            Route::middleware(['permission:menu_store.operations.for_delivery_today.update'])->post('for-delivery-today/upload/shipping-label/{id}', [ForDeliveryTodayController::class, 'uploadShippingLabel']);
            Route::middleware(['permission:menu_store.operations.for_delivery_today.update'])->post('for-delivery-today/bulk-upload/shipping-label', [ForDeliveryTodayController::class, 'uploadBulkShippingLabel']);
            Route::middleware(['permission:menu_store.operations.for_delivery_today.update'])->put('for-delivery-today/edit', [ForDeliveryTodayController::class, 'update']);
            Route::middleware(['permission:menu_store.operations.for_delivery_today.update'])->delete('for-delivery-today/upload/shipping-label/delete', [ForDeliveryTodayController::class, 'deleteShippingLabel']);
            Route::middleware(['permission:menu_store.operations.for_delivery_today.delete'])->post('for-delivery-today/delete', [ForDeliveryTodayController::class, 'delete']);
            Route::middleware(['permission:menu_store.operations.for_delivery_today.update'])->post('for-delivery-today/update-completed', [ForDeliveryTodayController::class, 'updateCompleted']);
            
            // Meetings
            Route::get('{id}/meetings/{year}/{month_number}', [MeetingController::class, 'index']);
            Route::post('/meetings/data', [MeetingController::class, 'data']);
            Route::middleware(['permission:menu_store.operations.meetings.create'])
                ->post('{id}/meetings/add', [MeetingController::class, 'store']);
            Route::middleware(['permission:menu_store.operations.meetings.update'])
                ->post('{id}/meetings/edit', [MeetingController::class, 'update']);
            Route::middleware(['permission:menu_store.operations.meetings.delete'])
                ->post('{id}/meetings/delete', [MeetingController::class, 'delete']);
        });

        /**
         * Financial Reports
         */
        Route::prefix('financial-reports')->group(function () {
            // Documents
            Route::get('{id}/documents/all/{page_id}', [DocumentController::class, 'all']);
            Route::delete('{id}/documents/delete', [DocumentController::class, 'delete']);
            Route::prefix('{id}/documents/{page_id}')->group(function () {
                Route::get('/', [DocumentController::class, 'index']);
                Route::post('data', [DocumentController::class, 'data']);
                Route::post('add', [DocumentController::class, 'store']);
                Route::put('edit', [DocumentController::class, 'update']);
            });

            Route::get('{id}/all-files', [AllFilesController::class, 'index']);

            // All Files
            Route::get('{id}/all-files', [AllFilesController::class, 'index']);
            Route::post('/all-files/data', [AllFilesController::class, 'data']);
            Route::post('{id}/all-files/add', [AllFilesController::class, 'store']);
            Route::post('{id}/all-files/edit', [AllFilesController::class, 'update']);
            Route::post('{id}/all-files/delete', [AllFilesController::class, 'delete']);

        });

        /**
         * Procurement
         */
        Route::prefix('procurement')->group(function () {
            // clinical
            Route::get('{id}/clinical-orders', [ClinicalOrderController::class, 'index']);
            Route::post('/clinical-orders/data', [ClinicalOrderController::class, 'data']);
            Route::middleware(['permission:menu_store.procurement.clinical_orders.create'])->post('{id}/clinical-orders/add', [ClinicalOrderController::class, 'store']);
            Route::middleware(['permission:menu_store.procurement.clinical_orders.update'])->post('{id}/clinical-orders/edit', [ClinicalOrderController::class, 'update']);
            Route::middleware(['permission:menu_store.procurement.clinical_orders.delete'])->delete('/clinical-orders/destroy', [ClinicalOrderController::class, 'destroy']);
            Route::put('/clinical-orders/edit-item', [ClinicalOrderController::class, 'update_item']);
            Route::delete('/clinical-orders/delete', [ClinicalOrderController::class, 'delete']);
            Route::delete('clinical-orders/delete_file', [ClinicalOrderController::class, 'delete_file']);
            Route::post('clinical-orders/file-upload', [ClinicalOrderController::class, 'file_upload']);
            Route::post('clinical-orders', [ClinicalOrderController::class, 'filteredData'])->name('clinicalOrder.index');
            Route::put('clinical-orders/statusUpdate', [ClinicalOrderController::class, 'statusUpdate'])->name('status.update');
            // pharmacy
            Route::prefix('{id}/pharmacy')->group(function () {
                Route::get('drug-orders', [PharmacyDrugOrderController::class, 'index']);
                Route::get('drug-order-invoices/{year}/{month_number}', [DrugOrderInvoiceController::class, 'index']);
                Route::get('supply-orders', [PharmacySupplyOrderController::class, 'index']);
                Route::get('wholesale-drug-returns', [PharmacyWholesaleDrugReturnController::class, 'index']);
                Route::get('inmar-returns', [InmarController::class, 'index']);
            });
            Route::prefix('pharmacy')->group(function () {
                // supply orders
                Route::post('{id}/supply-orders/data', [PharmacySupplyOrderController::class, 'data']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.supplies_orders.create'])->post('supply-orders/add', [PharmacySupplyOrderController::class, 'store']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.supplies_orders.update|menu_store.procurement.pharmacy.supplies_orders.updateall|menu_store.procurement.pharmacy.supplies_orders.updateactualqty'])->post('supply-orders/edit', [PharmacySupplyOrderController::class, 'update']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.supplies_orders.delete'])->post('supply-orders/delete', [PharmacySupplyOrderController::class, 'delete']);
                Route::post('supply-order-item/add', [PharmacySupplyOrderItemController::class, 'store']);
                Route::put('supply-order-item/edit', [PharmacySupplyOrderItemController::class, 'update']);
                Route::delete('supply-order-item/delete', [PharmacySupplyOrderItemController::class, 'delete']);
                Route::delete('supply-orders/delete_file', [PharmacySupplyOrderController::class, 'delete_file']);
                Route::post('supply-orders/file-upload', [PharmacySupplyOrderController::class, 'file_upload']);
                Route::post('supply-orders', [PharmacySupplyOrderController::class, 'filteredData'])->name('supplyOrder.index');

                // drug orders
                Route::post('drug-orders/data', [PharmacyDrugOrderController::class, 'data']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.drug_orders.create'])->post('drug-orders/add', [PharmacyDrugOrderController::class, 'store']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.drug_orders.update'])->post('drug-orders/edit', [PharmacyDrugOrderController::class, 'update']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.drug_orders.delete'])->post('drug-orders/delete', [PharmacyDrugOrderController::class, 'delete']);
                Route::post('drug-order-item/add', [PharmacyDrugOrderItemController::class, 'store']);
                Route::put('drug-order-item/edit', [PharmacyDrugOrderItemController::class, 'update']);
                Route::delete('drug-order-item/delete', [PharmacyDrugOrderItemController::class, 'delete']);
                Route::delete('drug-orders/delete_file', [PharmacyDrugOrderController::class, 'delete_file']);
                Route::post('drug-orders/file-upload', [PharmacyDrugOrderController::class, 'file_upload']);
                Route::post('drug-orders', [PharmacyDrugOrderController::class, 'filteredData'])->name('drugOrder.index');

                // drug orders invoices
                Route::post('drug-order-invoice/data', [DrugOrderInvoiceController::class, 'data']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.drug_order_invoice.create'])->post('drug-order-invoice/add', [DrugOrderInvoiceController::class, 'store']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.drug_order_invoice.update'])->post('drug-order-invoice/edit', [DrugOrderInvoiceController::class, 'update']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.drug_order_invoice.delete'])->post('drug-order-invoice/delete', [DrugOrderInvoiceController::class, 'delete']);
            
                // wholesaler drug return
                Route::post('wholesale-drug-returns/data', [PharmacyWholesaleDrugReturnController::class, 'data']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.wholesale_drug_returns.create'])->post('wholesale-drug-returns/add', [PharmacyWholesaleDrugReturnController::class, 'store']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.wholesale_drug_returns.update'])->post('wholesale-drug-returns/edit', [PharmacyWholesaleDrugReturnController::class, 'update']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.wholesale_drug_returns.delete'])->post('wholesale-drug-returns/delete', [PharmacyWholesaleDrugReturnController::class, 'delete']);
                Route::delete('wholesale-drug-returns/delete_file', [PharmacyWholesaleDrugReturnController::class, 'delete_file']);
                Route::post('wholesale-drug-returns/file-upload', [PharmacyWholesaleDrugReturnController::class, 'file_upload']);
                Route::post('wholesale-drug-returns', [PharmacyWholesaleDrugReturnController::class, 'filteredData'])->name('wholesaleDrugReturn.index');

                // inmar returns
                Route::post('{id}/inmar-returns/data', [InmarController::class, 'get_data']);
                // Route::middleware(['permission:menu_store.procurement.pharmacy.inmar_returns.create'])->post('inmar-returns/add', [InmarController::class, 'store']);
                // Route::middleware(['permission:menu_store.procurement.pharmacy.inmar_returns.update'])->post('inmar-returns/edit', [InmarController::class, 'update']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.inmar_returns.delete'])->post('inmar-returns/delete', [InmarController::class, 'destroy']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.inmar_returns.update'])->put('inmar-returns/edit-item', [InmarController::class, 'update_item']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.inmar_returns.update'])->post('inmar-returns/edit', [InmarController::class, 'update']);
                Route::middleware(['permission:menu_store.procurement.pharmacy.inmar_returns.update'])->delete('inmar-returns/delete', [InmarController::class, 'delete']);
                Route::delete('inmar-returns/delete_file', [InmarController::class, 'delete_file']);
                Route::get('inmar-returns/get_return_type_data', [InmarController::class, 'get_return_type']);
                Route::get('inmar-returns/get_status_data', [InmarController::class, 'get_status']);
                Route::post('inmar-returns/get_medication_data', [InmarController::class, 'get_medications']);
                Route::post('inmar-returns/get_clinic_data', [InmarController::class, 'get_clinics']);
                Route::post('inmar-returns/store', [InmarController::class, 'store']);
                Route::post('inmar-returns/data', [InmarController::class, 'get_data']);
                Route::post('inmar-returns/update', [InmarController::class, 'update']);
                Route::post('inmar-returns/delete', [InmarController::class, 'destroy']);
                Route::get('inmar-returns/get_inmar_medications_data/{id}', [InmarController::class, 'get_inmar_medications']);
                Route::post('inmar-returns/file-upload', [InmarController::class, 'file_upload']);
                Route::post('inmar-returns', [InmarController::class, 'filteredData'])->name('inmarReturns.index');
            });


            // drug recall notifications
            Route::get('{id}/drug-recall-notifications', [DrugRecallNotificationController::class, 'index']);
            Route::prefix('drug-recall-notifications')->group(function () {
                Route::post('data', [DrugRecallNotificationController::class, 'data']);
                Route::post('/documents/data', [DrugRecallNotificationController::class, 'documents']);
                Route::post('/upload', [DrugRecallNotificationController::class, 'upload']);

                Route::middleware(['permission:menu_store.procurement.drug_recall_notifications.create'])
                    ->post('add', [DrugRecallNotificationController::class, 'store']);

                Route::middleware(['permission:menu_store.procurement.drug_recall_notifications.update'])
                    ->put('edit', [DrugRecallNotificationController::class, 'update']);

                Route::middleware(['permission:menu_store.procurement.drug_recall_notifications.delete'])
                    ->delete('delete', [DrugRecallNotificationController::class, 'delete']);
            });
            // drug recall notification item
            Route::prefix('drug-recall-notification-item')->group(function () {
                Route::middleware(['permission:menu_store.procurement.drug_recall_notifications.update'])
                    ->post('add', [DrugRecallNotificationItemController::class, 'store']);

                Route::middleware(['permission:menu_store.procurement.drug_recall_notifications.update'])
                    ->post('edit', [DrugRecallNotificationItemController::class, 'update']);

                Route::middleware(['permission:menu_store.procurement.drug_recall_notifications.update'])
                    ->delete('delete', [DrugRecallNotificationItemController::class, 'delete']);
            });

        });

        /**
         * Financial Reports now renamed to **Data Insights**
         */
        Route::prefix('data-insights')->group(function () {
            //Pharmacy Gross Revenue
            Route::middleware(['permission:menu_store.data_insights.pgr.index|menu_store.data_insights.pgr.create|menu_store.data_insights.pgr.update|menu_store.data_insights.pgr.delete'])->get('{id}/pgr', [PharmacyGrossRevenueController::class, 'index']);
            //Route::get('{id}/pgr', [PharmacyGrossRevenueController::class, 'index']);
            Route::get('{id}/pgr/data', [PharmacyGrossRevenueController::class, 'get_data']);
            Route::get('{id}/pgr/get_store_data', [PharmacyGrossRevenueController::class, 'get_stores']);
            Route::middleware(['permission:menu_store.data_insights.pgr.create'])->post('{id}/pgr/store', [PharmacyGrossRevenueController::class, 'store']);
            Route::middleware(['permission:menu_store.data_insights.pgr.update'])->post('{id}/pgr/update', [PharmacyGrossRevenueController::class, 'update']);
            Route::middleware(['permission:menu_store.data_insights.pgr.delete'])->delete('{id}/pgr/delete', [PharmacyGrossRevenueController::class, 'destroy']);
            Route::get('{id}/pgr/download/{did}', [PharmacyGrossRevenueController::class, 'download']);

            //Payments Overview
            Route::middleware(['permission:menu_store.data_insights.payments_overview.index|menu_store.data_insights.payments_overview.create|menu_store.data_insights.payments_overview.update|menu_store.data_insights.payments_overview.delete'])->get('{id}/payments-overview', [PaymentOverviewController::class, 'index']);
            //Route::get('{id}/payments-overview', [PaymentOverviewController::class, 'index']);
            Route::get('{id}/payments-overview/data', [PaymentOverviewController::class, 'get_data']);
            Route::get('{id}/payments-overview/get_statuses', [PaymentOverviewController::class, 'get_statuses']);
            Route::middleware(['permission:menu_store.data_insights.payments_overview.create'])->post('{id}/payments-overview/store', [PaymentOverviewController::class, 'store']);
            Route::middleware(['permission:menu_store.data_insights.payments_overview.update'])->post('{id}/payments-overview/update', [PaymentOverviewController::class, 'update']);
            Route::middleware(['permission:menu_store.data_insights.payments_overview.delete'])->delete('{id}/payments-overview/delete', [PaymentOverviewController::class, 'destroy']);
            Route::get('{id}/payments-overview/download/{did}', [PaymentOverviewController::class, 'download']);

            //Collected Payments
            Route::middleware(['permission:menu_store.data_insights.collected_payments.index|menu_store.data_insights.collected_payments.create|menu_store.data_insights.collected_payments.update|menu_store.data_insights.collected_payments.delete'])->get('{id}/collected-payments', [CollectedPaymentsController::class, 'index']);
            Route::middleware(['permission:menu_store.data_insights.collected_payments.create'])->post('collected-payments/upload', [CollectedPaymentsController::class, 'upload']);
            Route::get('collected-payments/data', [CollectedPaymentsController::class, 'get_data']);
            Route::middleware(['permission:menu_store.data_insights.collected_payments.update'])->post('collected-payments/update', [CollectedPaymentsController::class, 'update']);
            Route::middleware(['permission:menu_store.data_insights.collected_payments.delete'])->delete('collected-payments/delete', [CollectedPaymentsController::class, 'destroy']);
            
            //Gross Revenue and Cogs (renamed to Completed Sales completed_sales completed sales)
            Route::middleware(['permission:menu_store.data_insights.gross_revenue_and_cogs.index|menu_store.data_insights.gross_revenue_and_cogs.create|menu_store.data_insights.gross_revenue_and_cogs.update|menu_store.data_insights.gross_revenue_and_cogs.delete'])->get('{id}/gross-revenue-and-cogs', [GrossRevenueAndCogsController::class, 'index']);
            Route::middleware(['permission:menu_store.data_insights.gross_revenue_and_cogs.create'])->post('gross-revenue-and-cogs/upload', [GrossRevenueAndCogsController::class, 'upload']);
            Route::get('gross-revenue-and-cogs/data', [GrossRevenueAndCogsController::class, 'get_data']);
            Route::middleware(['permission:menu_store.data_insights.gross_revenue_and_cogs.update'])->post('gross-revenue-and-cogs/update', [GrossRevenueAndCogsController::class, 'update']);
            Route::middleware(['permission:menu_store.data_insights.gross_revenue_and_cogs.delete'])->delete('gross-revenue-and-cogs/delete', [GrossRevenueAndCogsController::class, 'destroy']);
            Route::get('gross-revenue-and-cogs/chart-data', [GrossRevenueAndCogsController::class, 'chartData']);

            // Gross Sales
            Route::middleware(['permission:menu_store.data_insights.gross_sales.index|menu_store.data_insights.gross_sales.create|menu_store.data_insights.gross_sales.update|menu_store.data_insights.gross_sales.delete'])
                ->get('{id}/gross-sales', [GrossSalesController::class, 'index']);
            Route::middleware(['permission:menu_store.data_insights.gross_sales.create'])
                ->post('{id}/gross-sales/import', [GrossSalesController::class, 'import']);
            Route::middleware(['permission:menu_store.data_insights.gross_sales.create'])
                ->post('gross-sales/upload', [GrossSalesController::class, 'upload']);
            Route::get('gross-sales/data', [GrossSalesController::class, 'data']);
            Route::middleware(['permission:menu_store.data_insights.gross_sales.update'])
                ->post('gross-sales/update', [GrossSalesController::class, 'update']);
            Route::middleware(['permission:menu_store.data_insights.gross_sales.delete'])
                ->delete('gross-sales/delete', [GrossSalesController::class, 'delete']);
            Route::get('gross-sales/chart-data', [GrossSalesController::class, 'chartData']);
            
            //Account Receivables
            Route::get('{id}/account-receivables', [AccountReceivablesController::class, 'index']);
            Route::get('account-receivables/data', [AccountReceivablesController::class, 'data']);
            Route::middleware(['permission:menu_store.data_insights.account_receivables.create'])->post('account-receivables/upload', [AccountReceivablesController::class, 'upload']);
            Route::middleware(['permission:menu_store.data_insights.account_receivables.update'])->post('account-receivables/update', [AccountReceivablesController::class, 'update']);
            Route::middleware(['permission:menu_store.data_insights.account_receivables.delete'])->delete('account-receivables/delete', [AccountReceivablesController::class, 'destroy']);
        });

        /**
         * Compliance
         */
        Route::prefix('compliance')->group(function () {
            // Audit
            Route::get('{id}/audit', [ComplianceAuditController::class, 'index']);
            Route::post('/audit/data', [ComplianceAuditController::class, 'data']);
            Route::middleware(['permission:menu_store.cnr.audit.create'])->post('{id}/audit/add', [ComplianceAuditController::class, 'store']);
            Route::middleware(['permission:menu_store.cnr.audit.update'])->post('{id}/audit/edit', [ComplianceAuditController::class, 'update']);
            Route::middleware(['permission:menu_store.cnr.audit.delete'])->post('/audit/delete', [ComplianceAuditController::class, 'delete']);

            Route::get('{id}/self-audit-documents/{year}/{month_number}', [SelfAuditDocumentController::class, 'index']);
            Route::middleware(['permission:menu_store.compliance.monthly_control_counts.create'])->post('{id}/self-audit-documents/add', [SelfAuditDocumentController::class, 'store']);
            Route::middleware(['permission:menu_store.compliance.monthly_control_counts.update'])->put('{id}/self-audit-documents/edit', [SelfAuditDocumentController::class, 'update']);
            Route::middleware(['permission:menu_store.compliance.monthly_control_counts.delete'])->delete('{id}/self-audit-documents/delete', [SelfAuditDocumentController::class, 'delete']);
            Route::post('self-audit-documents/data', [SelfAuditDocumentController::class, 'data']);

            // Documents
            // Route::get('{id}/documents', [ComplianceDocumentController::class, 'index']);
            // Route::post('/documents/data', [ComplianceDocumentController::class, 'data']);
            // Route::middleware(['permission:menu_store.cnr.documents.create'])->post('{id}/documents/add', [ComplianceDocumentController::class, 'store']);
            // Route::middleware(['permission:menu_store.cnr.documents.update'])->post('{id}/documents/edit', [ComplianceDocumentController::class, 'update']);
            // Route::middleware(['permission:menu_store.cnr.documents.delete'])->post('/documents/delete', [ComplianceDocumentController::class, 'delete']);

            Route::prefix('{id}/self-audit-documents')->group(function () {
                Route::get('monthly-pharmacy-dfiqa', [MonthlyPharmacyDfiqaController::class, 'index']);
                Route::get('monthly-ihs-audit-checklist', [MonthlyIhsAuditChecklistController::class, 'index']);
                Route::get('monthly-self-assessment-qa', [MonthlySelfAssessmentQaController::class, 'index']);
            });
            Route::prefix('self-audit-documents')->group(function () {
                // Monthly Pharmacy DFI/QA
                Route::post('monthly-pharmacy-dfiqa/data', [MonthlyPharmacyDfiqaController::class, 'data']);
                Route::middleware(['permission:menu_store.cnr.self_audit_documents.m_p_dfiqa.create'])->post('monthly-pharmacy-dfiqa/add', [MonthlyPharmacyDfiqaController::class, 'store']);
                Route::middleware(['permission:menu_store.cnr.self_audit_documents.m_p_dfiqa.update'])->post('monthly-pharmacy-dfiqa/edit', [MonthlyPharmacyDfiqaController::class, 'update']);
                Route::middleware(['permission:menu_store.cnr.self_audit_documents.m_p_dfiqa.delete'])->post('monthly-pharmacy-dfiqa/delete', [MonthlyPharmacyDfiqaController::class, 'delete']);

                // Monthly IHS Audit Checklist
                Route::post('monthly-ihs-audit-checklist/data', [MonthlyIhsAuditChecklistController::class, 'data']);
                Route::middleware(['permission:menu_store.cnr.self_audit_documents.m_ihs_a_c.create'])->post('monthly-ihs-audit-checklist/add', [MonthlyIhsAuditChecklistController::class, 'store']);
                Route::middleware(['permission:menu_store.cnr.self_audit_documents.m_ihs_a_c.update'])->post('monthly-ihs-audit-checklist/edit', [MonthlyIhsAuditChecklistController::class, 'update']);
                Route::middleware(['permission:menu_store.cnr.self_audit_documents.m_ihs_a_c.delete'])->post('monthly-ihs-audit-checklist/delete', [MonthlyIhsAuditChecklistController::class, 'delete']);

                // Monthly Self Assessment QA
                Route::post('monthly-self-assessment-qa/data', [MonthlySelfAssessmentQaController::class, 'data']);
                Route::middleware(['permission:menu_store.cnr.self_audit_documents.m_s_a_qa.create'])->post('monthly-self-assessment-qa/add', [MonthlySelfAssessmentQaController::class, 'store']);
                Route::middleware(['permission:menu_store.cnr.self_audit_documents.m_s_a_qa.update'])->post('monthly-self-assessment-qa/edit', [MonthlySelfAssessmentQaController::class, 'update']);
                Route::middleware(['permission:menu_store.cnr.self_audit_documents.m_s_a_qa.delete'])->post('monthly-self-assessment-qa/delete', [MonthlySelfAssessmentQaController::class, 'delete']);
            });

            // Inventory Reconciliation
            // Route::get('{id}/inventory-reconciliation', [InventoryReconciliationDocumentController::class, 'index']);
            // Route::post('/inventory-reconciliation/data', [InventoryReconciliationDocumentController::class, 'data']);
            // Route::middleware(['permission:menu_store.cnr.inventory_reconciliation.create'])->post('{id}/inventory-reconciliation/add', [InventoryReconciliationDocumentController::class, 'store']);
            // Route::middleware(['permission:menu_store.cnr.inventory_reconciliation.update'])->post('{id}/inventory-reconciliation/edit', [InventoryReconciliationDocumentController::class, 'update']);
            // Route::middleware(['permission:menu_store.cnr.inventory_reconciliation.delete'])->post('/inventory-reconciliation/delete', [InventoryReconciliationDocumentController::class, 'delete']);

            Route::prefix('{id}/inventory-reconciliation-documents')->group(function () {
                Route::get('monthly-control-counts', [MonthlyControlCountsController::class, 'index']);
                Route::get('high-dollar-inventory-valuation', [HighDollarInventoryValuationController::class, 'index']);
            });
            Route::prefix('inventory-reconciliation-documents')->group(function () {
                // Monthly Control Counts
                Route::post('monthly-control-counts/data', [MonthlyControlCountsController::class, 'data']);
                Route::middleware(['permission:menu_store.cnr.inventory_reconciliation.m_c_c.create'])->post('monthly-control-counts/add', [MonthlyControlCountsController::class, 'store']);
                Route::middleware(['permission:menu_store.cnr.inventory_reconciliation.m_c_c.update'])->post('monthly-control-counts/edit', [MonthlyControlCountsController::class, 'update']);
                Route::middleware(['permission:menu_store.cnr.inventory_reconciliation.m_c_c.delete'])->post('monthly-control-counts/delete', [MonthlyControlCountsController::class, 'delete']);

                // Monthly IHS Audit Checklist
                Route::post('high-dollar-inventory-valuation/data', [HighDollarInventoryValuationController::class, 'data']);
                Route::middleware(['permission:menu_store.cnr.inventory_reconciliation.h_d_i_v.create'])->post('high-dollar-inventory-valuation/add', [HighDollarInventoryValuationController::class, 'store']);
                Route::middleware(['permission:menu_store.cnr.inventory_reconciliation.h_d_i_v.update'])->post('high-dollar-inventory-valuation/edit', [HighDollarInventoryValuationController::class, 'update']);
                Route::middleware(['permission:menu_store.cnr.inventory_reconciliation.h_d_i_v.delete'])->post('high-dollar-inventory-valuation/delete', [HighDollarInventoryValuationController::class, 'delete']);
            });

            // OIG Check
            Route::get('{id}/oig-check', [OIGCheckController::class, 'index']);
            Route::post('/oig-check/data', [OIGCheckController::class, 'data']);
            Route::post('{id}/oig-check/add', [OIGCheckController::class, 'store']);
            Route::post('{id}/oig-check/edit', [OIGCheckController::class, 'update']);
            Route::post('/oig-check/delete', [OIGCheckController::class, 'delete']);
            Route::get('/oig-check/update-oig', [OIGCheckController::class, 'downloadOigCsv']);
            Route::get('/oig-check/update-offline', [OIGCheckController::class, 'reloadOigCsv']);

            // OIG Documents
            Route::get('{id}/oig-documents', [OIGDocumentController::class, 'index']);
            Route::post('/oig-documents/data', [OIGDocumentController::class, 'data']);
            Route::middleware(['permission:menu_store.cnr.oig_documents.create'])->post('{id}/oig-documents/add', [OIGDocumentController::class, 'store']);
            Route::middleware(['permission:menu_store.cnr.oig_documents.update'])->post('{id}/oig-documents/edit', [OIGDocumentController::class, 'update']);
            Route::middleware(['permission:menu_store.cnr.oig_documents.delete'])->post('/oig-documents/delete', [OIGDocumentController::class, 'delete']);
        });


        /**
         * Escalation
         */
        Route::prefix('escalation')->group(function () {
            // Tickets
            Route::get('{id}/tickets', [TicketController::class, 'index']);
            Route::get('{id}/tickets/show/{tix_id}', [TicketController::class, 'show']);
            Route::post('tickets/data', [TicketController::class, 'data']);
            Route::post('tickets/assignees', [TicketController::class, 'assignees']);
            Route::post('tickets/watchers', [TicketController::class, 'watchers']);
            Route::post('tickets', [TicketController::class, 'filteredData'])->name('escalationTicket.index');
            
            Route::post('ticket-documents/data', [TicketDocumentController::class, 'data']);
           
            Route::middleware(['permission:menu_store.escalation.tickets.create'])->group(function () {
                Route::post('{id}/tickets/add', [TicketController::class, 'store']);
                Route::post('ticket-documents/add', [TicketDocumentController::class, 'store']);
            });

            Route::middleware(['permission:menu_store.escalation.tickets.update|menu_store.escalation.tickets.semi_update'])->group(function () {
                Route::post('{id}/tickets/edit', [TicketController::class, 'update']);
                
                
                Route::prefix('tickets')->group(function () {
                    Route::get('load-attachments/{ticket_id}', [TicketController::class, 'loadAttachments']);
                    Route::get('load-comments/{ticket_id}', [TicketController::class, 'loadComments']);
                    Route::post('store-attachments', [TicketController::class, 'storeAttachments']);
                    Route::post('store-comment', [TicketController::class, 'storeComment']);
                    Route::patch('update-details', [TicketController::class, 'updateDetails']);
                    Route::post('update-attachments', [TicketController::class, 'updateAttachments']);
                    Route::post('watchers/add', [TicketController::class, 'addWatcher']);
                    Route::delete('watchers/delete', [TicketController::class, 'deleteWatcher']);
                });

            });

            Route::middleware(['permission:menu_store.escalation.tickets.delete'])->group(function () {
                Route::post('tickets/delete', [TicketController::class, 'delete']);
                Route::put('tickets/archive', [TicketController::class, 'archive']);
                Route::put('tickets/unarchive', [TicketController::class, 'unarchive']);
                Route::post('ticket-documents/delete', [TicketDocumentController::class, 'delete']);
            });

            // Reviews
            Route::prefix('{id}/reviews')->group(function () {
                Route::get('/', [ReviewController::class, 'index']);
                Route::post('data', [ReviewController::class, 'data']);
            });
        });

        /**
         * Patient Support
         */
        Route::prefix('patient-support')->group(function () {
            Route::middleware(['permission:menu_store.patient_support.transfer_rx.index'])->group(function () {
                Route::get('{id}/transfer_rx/{list_id}/tribe_members/', [TribeMemberController::class, 'index']);
                Route::get('{id}/transfer_rx/tribe_members/get_patient_data', [TribeMemberController::class, 'get_patient_data']);
                Route::get('{id}/transfer_rx/tribe_members/get_data', [TribeMemberController::class, 'get_data']);
                Route::get('{id}/transfer_rx/tribe_members/get_default_list', [TribeMemberController::class, 'get_default_list']);
                Route::put('{id}/transfer_rx/tribe_members/update_task', [TribeMemberController::class, 'update_task']);
                Route::put('{id}/transfer_rx/tribe_members/update_clicked_column', [TribeMemberController::class, 'update_clicked_column']);
                Route::post('{id}/transfer_rx/tribe_members/update_default', [TribeMemberController::class, 'update_default']);
                Route::post('{id}/transfer_rx/tribe_members/patient_store', [TribeMemberController::class, 'patient_store']);
                Route::get('{id}/transfer_rx/tribe_members/get_assignee_data', [TribeMemberController::class, 'get_assignee_data']);
                Route::delete('{id}/transfer_rx/tribe_members/delete_assignee', [TribeMemberController::class, 'delete_assignee']);
                Route::put('{id}/transfer_rx/tribe_members/update_assignees', [TribeMemberController::class, 'update_assignees']);
                Route::post('{id}/transfer_rx/tribe_members/comment_store', [TribeMemberController::class, 'comment_store']);
                Route::get('{id}/transfer_rx/tribe_members/get_countries', [TribeMemberController::class, 'get_countries']);
                Route::post('{id}/transfer_rx/tribe_members/file_upload', [TribeMemberController::class, 'file_upload']);
                Route::delete('{id}/transfer_rx/tribe_members/delete_file', [TribeMemberController::class, 'delete_file']);
                Route::get('{id}/transfer_rx/tribe_members/file_download/{file_id}', [TribeMemberController::class, 'file_download']);
                Route::get('{id}/transfer_rx/tribe_members/get_shipping_type', [TribeMemberController::class, 'get_shipping_type']);    
                Route::put('{id}/transfer_rx/tribe_members/update_shipping_type', [TribeMemberController::class, 'update_shipping_type']);
                Route::post('{id}/transfer_rx/tribe_members/medication_store', [TribeMemberController::class, 'medication_store']);
                Route::get('{id}/transfer_rx/tribe_members/get_patient_medications', [TribeMemberController::class, 'get_patient_medications']);
                Route::put('{id}/transfer_rx/tribe_members/update_patient_medication', [TribeMemberController::class, 'update_patient_medication']);
                Route::delete('{id}/transfer_rx/tribe_members/delete_patient_medication', [TribeMemberController::class, 'delete_patient_medication']);
            });

            
        });

        /**
         * Clinical
         */
        Route::prefix('clinical')->group(function () {

            //KPI
            Route::get('{id}/kpi', [KpiController::class, 'index']);
            Route::prefix('kpi')->group(function () {
                Route::get('data', [KpiController::class, 'data']);
                Route::get('date_with', [KpiController::class, 'date_with']);
                Route::middleware(['permission:menu_store.clinical.kpi.create'])->post('add', [KpiController::class, 'store']);
                Route::middleware(['permission:menu_store.clinical.kpi.create'])->put('edit', [KpiController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.kpi.create'])->delete('delete', [KpiController::class, 'delete']);
            });

            //Outreach
            Route::get('{id}/outreach', [OutreachController::class, 'index']);
            Route::prefix('outreach')->group(function () {
                Route::get('data_schedule', [OutreachController::class, 'data_schedule']);
                Route::middleware(['permission:menu_store.clinical.outreach.create'])->post('add_schedule', [OutreachController::class, 'store_schedule']);
                Route::get('data', [OutreachController::class, 'data']);
                Route::get('date_with', [OutreachController::class, 'date_with']);
                Route::middleware(['permission:menu_store.clinical.outreach.create'])->post('add', [OutreachController::class, 'store']);
                Route::middleware(['permission:menu_store.clinical.outreach.update'])->put('edit', [OutreachController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.outreach.delete'])->delete('delete', [OutreachController::class, 'delete']);
            });

            //Prio Authorization
            Route::get('{id}/prio-authorization', [PrioAuthorizationController::class, 'index']);
            Route::prefix('prio-authorization')->group(function () {
                Route::get('data_schedule', [PrioAuthorizationController::class, 'data_schedule']);
                Route::middleware(['permission:menu_store.clinical.prio_authorization.create'])->post('add_schedule', [PrioAuthorizationController::class, 'store_schedule']);
                Route::get('data', [PrioAuthorizationController::class, 'data']);
                Route::get('date_with', [PrioAuthorizationController::class, 'date_with']);
                Route::middleware(['permission:menu_store.clinical.prio_authorization.create'])->post('add', [PrioAuthorizationController::class, 'store']);
                Route::middleware(['permission:menu_store.clinical.prio_authorization.update'])->put('edit', [PrioAuthorizationController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.prio_authorization.delete'])->delete('delete', [PrioAuthorizationController::class, 'delete']);
            });

            // Pioneer Patients
            Route::get('{id}/dashboard', [ClinicalDashboardController::class, 'index']);
            Route::get('/dashboard/get-chart', [ClinicalDashboardController::class, 'chart']);
            Route::get('{id}/pioneer-patients', [PioneerPatientController::class, 'index']);
            Route::get('{id}/pioneer-patients/facesheet/{p_id}', [PioneerPatientController::class, 'facesheet']);
            Route::post('/pioneer-patients/data', [PioneerPatientController::class, 'data']);
            Route::middleware(['permission:menu_store.clinical.pioneer_patients.create'])
            ->post('/pioneer-patients/upload', [PioneerPatientController::class, 'upload']);

            
            Route::middleware(['permission:menu_store.clinical.mtm_outcomes_report.index'])->get('{id}/mtm-outcomes-reports', [OutcomeController::class, 'index']);
            Route::middleware(['permission:menu_store.clinical.mtm_outcomes_report.index'])->post('/mtm-outcomes-reports/data', [OutcomeController::class, 'get_data']);
            Route::middleware(['permission:menu_store.clinical.mtm_outcomes_report.delete'])->post('/mtm-outcomes-reports/delete', [OutcomeController::class, 'delete']);

            Route::get('{id}/adherence-reports/{year}', [AdherenceReportController::class, 'index']);
            Route::get('{id}/adherence-reports', [AdherenceReportController::class, 'index']);
            Route::middleware(['permission:menu_store.clinical.adherence_report.create'])->post('/adherence-reports/add', [AdherenceReportController::class, 'store']);

            //tebra patient
            Route::middleware(['permission:menu_store.clinical.tebra_patients.index'])->get('{id}/tebra-patients', [TebraPatientController::class, 'index']);
            Route::get('{id}/tebra-patients/facesheet/{p_id}', [TebraPatientController::class, 'facesheet']);
            Route::get('{id}/tebra-patients/medications/{p_id}', [TebraPatientController::class, 'get_medications']);
            Route::get('{id}/tebra-patients/allergies/{p_id}', [TebraPatientController::class, 'get_allergies']);
            Route::get('{id}/tebra-patients/demographics/{p_id}', [TebraPatientController::class, 'get_demographics']);
            Route::get('{id}/tebra-patients/notes/{p_id}', [TebraPatientController::class, 'get_notes']);
            Route::get('{id}/tebra-patients/immunization/{p_id}', [TebraPatientController::class, 'get_immunizations']);
            
            Route::get('/tebra-patients/get-logs/{p_no}/{cp_no}', [RingCentralController::class, 'getList']);
            Route::post('/tebra-patients/send-sms', [RingCentralController::class, 'sendSms']);

            Route::post('/tebra-patients/get_patient_allergies_data', [TebraPatientController::class, 'get_patient_allergies_data']);
            Route::get('{id}/tebra-patients/get_patient_medications_data', [TebraPatientController::class, 'get_patient_medications_data']);
            Route::get('/tebra-patients/get_patient_data/{id}', [TebraPatientController::class, 'get_patient_data']);
            Route::post('/tebra-patients/medications_store', [TebraPatientController::class, 'medications_store']);
            Route::post('/tebra-patients/medication_update', [TebraPatientController::class, 'medication_update']);
            Route::post('/tebra-patients/medication_delete', [TebraPatientController::class, 'medication_destroy']);
            Route::get('/tebra-patients/get_patient_allergies_data', [TebraPatientController::class, 'get_patient_allergies_data']);
            Route::post('/tebra-patients/allergy_store', [TebraPatientController::class, 'allergy_store']);
            Route::post('/tebra-patients/allergy_delete', [TebraPatientController::class, 'allergy_destroy']);
            Route::post('/tebra-patients/allergy_update', [TebraPatientController::class, 'allergy_update']); 
            Route::post('/tebra-patients/get_patient_notes_data', [TebraPatientController::class, 'get_patient_notes_data']);
            Route::post('/tebra-patients/note_store', [TebraPatientController::class, 'note_store']);
            Route::get('/tebra-patients/note_download/{id}', [TebraPatientController::class, 'note_download']);
            Route::post('/tebra-patients/note_delete', [TebraPatientController::class, 'note_destroy']);
            Route::post('/tebra-patients/note_update', [TebraPatientController::class, 'note_update']);
            Route::post('/tebra-patients/get_patient_immunizations_data', [TebraPatientController::class, 'get_patient_immunizations_data']);
            Route::post('/tebra-patients/immunization_store', [TebraPatientController::class, 'immunization_store']);
            Route::post('/tebra-patients/immunization_update', [TebraPatientController::class, 'immunization_update']);
            Route::post('/tebra-patients/immunization_delete', [TebraPatientController::class, 'immunization_destroy']);

            // Meetings
            Route::get('{id}/meetings/{year}/{month_number}', [ClinicalMeetingController::class, 'index']);
            Route::post('/meetings/data', [ClinicalMeetingController::class, 'data']);
            Route::middleware(['permission:menu_store.clinical.meetings.create'])
                ->post('{id}/meetings/add', [ClinicalMeetingController::class, 'store']);
            Route::middleware(['permission:menu_store.clinical.meetings.update'])
                ->post('{id}/meetings/edit', [ClinicalMeetingController::class, 'update']);
            Route::middleware(['permission:menu_store.clinical.meetings.delete'])
                ->post('{id}/meetings/delete', [ClinicalMeetingController::class, 'delete']);

            // Renewals
            Route::prefix('{id}/renewals')->group(function () {
                Route::get('/', [RenewalController::class, 'index']);
                Route::get('patient-data/{renewal_id}', [RenewalController::class, 'patientData']);
                Route::post('filter-board-data', [RenewalController::class, 'filterBoardData']);
                Route::middleware(['permission:menu_store.clinical.renewals.export'])
                    ->get('/export', [RenewalController::class, 'export']);
                Route::middleware(['permission:menu_store.clinical.renewals.create'])
                    ->post('import', [RenewalController::class, 'import']);
                Route::middleware(['permission:menu_store.clinical.renewals.update|menu_store.clinical.renewals.archive'])
                    ->patch('update', [RenewalController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.renewals.delete'])
                    ->delete('delete', [RenewalController::class, 'delete']);
                // comment
                Route::post('comment/store', [RenewalCommentController::class, 'store']);
            });

            // automations -------------------------------

            // Send pending refills
            Route::prefix('{id}/pending-refill-requests')->group(function () {
                Route::get('/', [PendingRefillRequestController::class, 'index']);
                Route::get('date-with', [PendingRefillRequestController::class, 'dateWith']);
                Route::middleware(['permission:menu_store.clinical.pending_refill_requests.create'])
                    ->post('import', [PendingRefillRequestController::class, 'import']);
                Route::middleware(['permission:menu_store.clinical.pending_refill_requests.delete'])
                    ->delete('delete-all', [PendingRefillRequestController::class, 'deleteAll']);
                Route::get('export', [PendingRefillRequestController::class, 'export']);
            });
            Route::prefix('pending-refill-requests')->group(function () {
                Route::get('data', [PendingRefillRequestController::class, 'data']);
                Route::post('summary', [PendingRefillRequestController::class, 'summary']);
                Route::post('send-mail', [PendingRefillRequestController::class, 'sendMail']);
                Route::middleware(['permission:menu_store.clinical.pending_refill_requests.create'])
                    ->post('add', [PendingRefillRequestController::class, 'store']);
                Route::middleware(['permission:menu_store.clinical.pending_refill_requests.update'])
                    ->put('edit', [PendingRefillRequestController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.pending_refill_requests.delete'])
                    ->delete('delete', [PendingRefillRequestController::class, 'delete']);
            });

            // Brand switching IOU
            Route::prefix('{id}/brand-switchings')->group(function () {
                Route::get('/', [BrandSwitchingController::class, 'index']);
                Route::get('date-with', [BrandSwitchingController::class, 'dateWith']);
                Route::middleware(['permission:menu_store.clinical.brand_switchings.create'])
                    ->post('import', [BrandSwitchingController::class, 'import']);
                Route::middleware(['permission:menu_store.clinical.brand_switchings.delete'])
                    ->delete('delete-all', [BrandSwitchingController::class, 'deleteAll']);
                Route::get('export', [BrandSwitchingController::class, 'export']);
            });
            Route::prefix('brand-switchings')->group(function () {
                Route::get('data', [BrandSwitchingController::class, 'data']);
                Route::post('summary', [BrandSwitchingController::class, 'summary']);
                Route::post('send-mail', [BrandSwitchingController::class, 'sendMail']);
                Route::middleware(['permission:menu_store.clinical.brand_switchings.create'])
                    ->post('add', [BrandSwitchingController::class, 'store']);
                Route::middleware(['permission:menu_store.clinical.brand_switchings.update'])
                    ->put('edit', [BrandSwitchingController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.brand_switchings.delete'])
                    ->delete('delete', [BrandSwitchingController::class, 'delete']);
            });

            // Therapy change and reco
            Route::prefix('{id}/therapy-change-and-reco')->group(function () {
                Route::get('/', [TherapyChangeAndRecoController::class, 'index']);
                Route::get('date-with', [TherapyChangeAndRecoController::class, 'dateWith']);
                Route::middleware(['permission:menu_store.clinical.therapy_change_and_reco.create'])
                    ->post('import', [TherapyChangeAndRecoController::class, 'import']);
                Route::middleware(['permission:menu_store.clinical.therapy_change_and_reco.delete'])
                    ->delete('delete-all', [TherapyChangeAndRecoController::class, 'deleteAll']);
                Route::get('export', [TherapyChangeAndRecoController::class, 'export']);
            });
            Route::prefix('therapy-change-and-reco')->group(function () {
                Route::get('data', [TherapyChangeAndRecoController::class, 'data']);
                Route::post('summary', [TherapyChangeAndRecoController::class, 'summary']);
                Route::post('send-mail', [TherapyChangeAndRecoController::class, 'sendMail']);
                Route::middleware(['permission:menu_store.clinical.therapy_change_and_reco.create'])
                    ->post('add', [TherapyChangeAndRecoController::class, 'store']);
                Route::middleware(['permission:menu_store.clinical.therapy_change_and_reco.update'])
                    ->put('edit', [TherapyChangeAndRecoController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.therapy_change_and_reco.delete'])
                    ->delete('delete', [TherapyChangeAndRecoController::class, 'delete']);
            });

            // Rx daily census
            Route::prefix('{id}/rx-daily-census')->group(function () {
                Route::get('/', [RxDailyCensusController::class, 'index']);
                Route::get('date-with', [RxDailyCensusController::class, 'dateWith']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_census.create'])
                    ->post('import', [RxDailyCensusController::class, 'import']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_census.delete'])
                    ->delete('delete-all', [RxDailyCensusController::class, 'deleteAll']);
                Route::get('export', [RxDailyCensusController::class, 'export']);
            });
            Route::prefix('rx-daily-census')->group(function () {
                Route::get('data', [RxDailyCensusController::class, 'data']);
                Route::post('summary', [RxDailyCensusController::class, 'summary']);
                Route::post('send-mail', [RxDailyCensusController::class, 'sendMail']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_census.create'])
                    ->post('add', [RxDailyCensusController::class, 'store']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_census.update'])
                    ->put('edit', [RxDailyCensusController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_census.delete'])
                    ->delete('delete', [RxDailyCensusController::class, 'delete']);
            });

            // Rx daily transfers
            Route::prefix('{id}/rx-daily-transfers/{status}')->group(function () {
                Route::get('/', [RxDailyTransferController::class, 'index']);
                Route::get('date-with', [RxDailyTransferController::class, 'dateWith']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_transfers.create'])
                    ->post('import', [RxDailyTransferController::class, 'import']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_transfers.delete'])
                    ->delete('delete-all', [RxDailyTransferController::class, 'deleteAll']);
                Route::get('export', [RxDailyTransferController::class, 'export']);
            });
            Route::prefix('rx-daily-transfers/{status}')->group(function () {
                Route::get('data', [RxDailyTransferController::class, 'data']);
                Route::post('summary', [RxDailyTransferController::class, 'summary']);
                Route::post('send-mail', [RxDailyTransferController::class, 'sendMail']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_transfers.create'])
                    ->post('add', [RxDailyTransferController::class, 'store']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_transfers.update'])
                    ->put('edit', [RxDailyTransferController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.rx_daily_transfers.delete'])
                    ->delete('delete', [RxDailyTransferController::class, 'delete']);
            });

            // Bridged patients
            Route::prefix('{id}/bridged-patients')->group(function () {
                Route::get('/', [BridgedPatientController::class, 'index']);
                Route::get('date-with', [BridgedPatientController::class, 'dateWith']);
                Route::middleware(['permission:menu_store.clinical.bridged_patients.create'])
                    ->post('import', [BridgedPatientController::class, 'import']);
                Route::middleware(['permission:menu_store.clinical.bridged_patients.delete'])
                    ->delete('delete-all', [BridgedPatientController::class, 'deleteAll']);
                Route::get('export', [BridgedPatientController::class, 'export']);
            });
            Route::prefix('bridged-patients')->group(function () {
                Route::get('data', [BridgedPatientController::class, 'data']);
                Route::post('summary', [BridgedPatientController::class, 'summary']);
                Route::post('send-mail', [BridgedPatientController::class, 'sendMail']);
                Route::middleware(['permission:menu_store.clinical.bridged_patients.create'])
                    ->post('add', [BridgedPatientController::class, 'store']);
                Route::middleware(['permission:menu_store.clinical.bridged_patients.update'])
                    ->put('edit', [BridgedPatientController::class, 'update']);
                Route::middleware(['permission:menu_store.clinical.bridged_patients.delete'])
                    ->delete('delete', [BridgedPatientController::class, 'delete']);
            });

        });

        /**
         * Knowledge Base
         */
        Route::prefix('knowledge-base')->group(function () {
            // All Files
            Route::get('{id}/all-files', [AllFilesController::class, 'index']);
            Route::post('/all-files/data', [AllFilesController::class, 'data']);
            Route::post('{id}/all-files/add', [AllFilesController::class, 'store']);
            Route::post('{id}/all-files/edit', [AllFilesController::class, 'update']);
            Route::post('{id}/all-files/delete', [AllFilesController::class, 'delete']);

            // SOPs
            Route::get('{id}/sops', [SOPController::class, 'index']);
            Route::post('/sops/data', [SOPController::class, 'data']);
            Route::middleware(['permission:menu_store.knowledge_base.sops.create'])->post('{id}/sops/add', [SOPController::class, 'store']);
            Route::middleware(['permission:menu_store.knowledge_base.sops.update'])->post('{id}/sops/edit', [SOPController::class, 'update']);
            Route::middleware(['permission:menu_store.knowledge_base.sops.delete'])->post('{id}/sops/delete', [SOPController::class, 'delete']);

            // P&Ps
            Route::get('{id}/pnps', [PNPController::class, 'index']);
            Route::post('/pnps/data', [PNPController::class, 'data']);
            Route::middleware(['permission:menu_store.knowledge_base.pnps.create'])->post('{id}/pnps/add', [PNPController::class, 'store']);
            Route::middleware(['permission:menu_store.knowledge_base.pnps.update'])->post('{id}/pnps/edit', [PNPController::class, 'update']);
            Route::middleware(['permission:menu_store.knowledge_base.pnps.delete'])->post('{id}/pnps/delete', [PNPController::class, 'delete']);

            // Processs Documents
            Route::get('{id}/process-documents', [ProcessDocumentsController::class, 'index']);
            Route::post('/process-documents/data', [ProcessDocumentsController::class, 'data']);
            Route::middleware(['permission:menu_store.knowledge_base.pd.create'])->post('{id}/process-documents/add', [ProcessDocumentsController::class, 'store']);
            Route::middleware(['permission:menu_store.knowledge_base.pd.update'])->post('{id}/process-documents/edit', [ProcessDocumentsController::class, 'update']);
            Route::middleware(['permission:menu_store.knowledge_base.pd.delete'])->post('{id}/process-documents/delete', [ProcessDocumentsController::class, 'delete']);

            // How To Guide
            Route::get('{id}/how-to-guide', [HowToGuideController::class, 'index']);
            Route::post('/how-to-guide/data', [HowToGuideController::class, 'data']);
            Route::middleware(['permission:menu_store.knowledge_base.htg.create'])->post('{id}/how-to-guide/add', [HowToGuideController::class, 'store']);
            Route::middleware(['permission:menu_store.knowledge_base.htg.update'])->post('{id}/how-to-guide/edit', [HowToGuideController::class, 'update']);
            Route::middleware(['permission:menu_store.knowledge_base.htg.delete'])->post('{id}/how-to-guide/delete', [HowToGuideController::class, 'delete']);

            // Board of Pharmacy
            Route::get('{id}/board-of-pharmacy', [BoardOfPharmacyController::class, 'index']);
            Route::post('/board-of-pharmacy/data', [BoardOfPharmacyController::class, 'data']);
            Route::middleware(['permission:menu_store.knowledge_base.bop.create'])->post('{id}/board-of-pharmacy/add', [BoardOfPharmacyController::class, 'store']);
            Route::middleware(['permission:menu_store.knowledge_base.bop.update'])->post('{id}/board-of-pharmacy/edit', [BoardOfPharmacyController::class, 'update']);
            Route::middleware(['permission:menu_store.knowledge_base.bop.delete'])->post('{id}/board-of-pharmacy/delete', [BoardOfPharmacyController::class, 'delete']);

            // Pharmacy Forms
            Route::get('{id}/pharmacy-forms', [PharmacyFormsController::class, 'index']);
            Route::post('/pharmacy-forms/data', [PharmacyFormsController::class, 'data']);
            Route::middleware(['permission:menu_store.knowledge_base.pf.create'])->post('{id}/pharmacy-forms/add', [PharmacyFormsController::class, 'store']);
            Route::middleware(['permission:menu_store.knowledge_base.pf.update'])->post('{id}/pharmacy-forms/edit', [PharmacyFormsController::class, 'update']);
            Route::middleware(['permission:menu_store.knowledge_base.pf.delete'])->post('{id}/pharmacy-forms/delete', [PharmacyFormsController::class, 'delete']);
        });

        /**
         * SOP > SOPs and P&Ps
         */
        Route::prefix('sop')->group(function () {
            // SOPs
            Route::get('{id}/sops', [SOPController::class, 'index']);
            Route::post('/sops/data', [SOPController::class, 'data']);
            Route::middleware(['permission:menu_store.sop.sops.create'])->post('{id}/sops/add', [SOPController::class, 'store']);
            Route::middleware(['permission:menu_store.sop.sops.update'])->post('{id}/sops/edit', [SOPController::class, 'update']);
            Route::middleware(['permission:menu_store.sop.sops.delete'])->post('/sops/delete', [SOPController::class, 'delete']);
            Route::get('/topics', [SOPController::class, 'get_topics']);
            
            // P&Ps
            Route::get('{id}/pnps', [PNPController::class, 'index']);
            Route::post('/pnps/data', [PNPController::class, 'data']);
            Route::middleware(['permission:menu_store.sop.pnps.create'])->post('{id}/pnps/add', [PNPController::class, 'store']);
            Route::middleware(['permission:menu_store.sop.pnps.update'])->post('{id}/pnps/edit', [PNPController::class, 'update']);
            Route::middleware(['permission:menu_store.sop.pnps.delete'])->post('/pnps/delete', [PNPController::class, 'delete']);
        });

        /**
         * EOD Register Report
         */
        Route::prefix('eod-register-report')->group(function () {
            // Register
            Route::get('{id}/register', [RegisterController::class, 'index']);
            Route::get('{id}/register/get_data', [RegisterController::class, 'get_data']);
            Route::get('{id}/register/get_file-data', [RegisterController::class, 'get_fileData']);
            Route::middleware(['permission:menu_store.eod_register_report.register.create'])->post('{id}/register/store', [RegisterController::class, 'store']);
            Route::middleware(['permission:menu_store.eod_register_report.register.update'])->post('{id}/register/update', [RegisterController::class, 'update']);
            Route::middleware(['permission:menu_store.eod_register_report.register.delete'])->delete('/register/delete', [RegisterController::class, 'destroy']);
            Route::middleware(['permission:menu_store.eod_register_report.register.update'])->post('{id}/register/file-upload', [RegisterController::class, 'fileUpload']);
            Route::delete('/register/delete_file', [RegisterController::class, 'delete_file']);

            Route::get('{id}/deposit', [DepositController::class, 'index']);
            Route::prefix('deposit')->group(function () {
                Route::get('data', [DepositController::class, 'data']);
                Route::post('pdf/{id}', [DepositController::class, 'pdf']);
                Route::get('download/{id}', [DepositController::class, 'download']);
                Route::middleware(['permission:menu_store.eod_register_report.deposit.create'])->post('add', [DepositController::class, 'store']);
                Route::middleware(['permission:menu_store.eod_register_report.deposit.create'])->put('edit', [DepositController::class, 'update']);
                Route::middleware(['permission:menu_store.eod_register_report.deposit.create'])->delete('delete', [DepositController::class, 'delete']);
            });
        });


        /**
         * Human Resource (within Store)
         */
        Route::prefix('human-resource')->group(function () {

            Route::get('{id}/organization', [OrganizationController::class, 'index']);

            // Employees
            Route::get('{id}/employees', [StoreEmployeeController::class, 'index']); // offshore
            Route::get('{id}/onshore', [StoreEmployeeController::class, 'onshore']);
            Route::get('{id}/onshore/org-chart', [StoreEmployeeController::class, 'onshoreOrganizationChart']);
            Route::get('{id}/offshore/org-chart', [StoreEmployeeController::class, 'offshoreOrganizationChart']);
            Route::prefix('employees')->group(function () {
                Route::post('data', [StoreEmployeeController::class, 'data']);
                Route::middleware(['permission:menu_store.hr.employees.create'])->post('add', [StoreEmployeeController::class, 'store']);
                Route::middleware(['permission:menu_store.hr.employees.update'])->put('edit', [StoreEmployeeController::class, 'update']);
                Route::middleware(['permission:menu_store.hr.employees.delete'])->delete('delete', [StoreEmployeeController::class, 'delete']);
                Route::middleware(['permission:menu_store.hr.employees.import'])->get('import', [StoreEmployeeController::class, 'import']);
            });

            // Scheudule
            Route::get('{id}/schedules/{is_offshore}/calendar', [ScheduleController::class, 'index']);
            Route::middleware(['permission:menu_store.hr.schedules.export'])
                ->get('{id}/schedules/export-onshore/{date}', [ScheduleController::class, 'exportOnshoreByMonthYear']);
            Route::prefix('schedules')->group(function () {
                Route::post('data', [ScheduleController::class, 'data']);
                Route::post('staff', [ScheduleController::class, 'staff']);
                Route::post('staff/data', [ScheduleController::class, 'staffData']);
                Route::middleware(['permission:menu_store.hr.schedules.create'])->post('add', [ScheduleController::class, 'store']);
                Route::middleware(['permission:menu_store.hr.schedules.update'])->put('edit', [ScheduleController::class, 'update']);
                Route::middleware(['permission:menu_store.hr.schedules.delete'])->delete('delete', [ScheduleController::class, 'delete']);
                Route::middleware(['permission:menu_store.hr.schedules.import'])->post('import', [ScheduleController::class, 'import']);
            });
        });

        /**
         * Inventory Reconciliation
         */
        Route::prefix('inventory-reconciliation')->group(function () {
            // Daily Inventory Evaluation
            Route::get('{id}/daily-inventory-evaluation', [DailyInventoryEvaluationController::class, 'index']);
            Route::prefix('daily-inventory-evaluation')->group(function () {
                Route::post('data', [DailyInventoryEvaluationController::class, 'data']);
                Route::middleware(['permission:menu_store.inventory_reconciliation.daily.create'])->post('add', [DailyInventoryEvaluationController::class, 'store']);
                Route::middleware(['permission:menu_store.inventory_reconciliation.daily.delete'])->delete('delete', [DailyInventoryEvaluationController::class, 'delete']);
            });
            // Inventory Audit (Weekly)
            Route::get('{id}/weekly-inventory-audit', [WeeklyInventoryAuditController::class, 'index']);
            Route::prefix('weekly-inventory-audit')->group(function () {
                Route::post('data', [WeeklyInventoryAuditController::class, 'data']);
                Route::middleware(['permission:menu_store.inventory_reconciliation.weekly.create'])->post('add', [WeeklyInventoryAuditController::class, 'store']);
                Route::middleware(['permission:menu_store.inventory_reconciliation.weekly.delete'])->delete('delete', [WeeklyInventoryAuditController::class, 'delete']);
            });
            // MONTHLY
            Route::get('{id}/monthly-control-counts/c2', [MonthlyControlCountsC2Controller::class, 'index']);
            Route::get('{id}/monthly-control-counts/c3-5', [MonthlyControlCountsC3To5Controller::class, 'index']);
            Route::prefix('monthly-control-counts')->group(function () {
                // Control Counts > C2
                Route::prefix('c2')->group(function () {
                    Route::post('data', [MonthlyControlCountsC2Controller::class, 'data']);
                    Route::middleware(['permission:menu_store.inventory_reconciliation.monthly.c2.create'])->post('add', [MonthlyControlCountsC2Controller::class, 'store']);
                    Route::middleware(['permission:menu_store.inventory_reconciliation.monthly.c2.delete'])->delete('delete', [MonthlyControlCountsC2Controller::class, 'delete']);
                });
                // Control Counts > C3 - 5
                Route::prefix('c3-5')->group(function () {
                    Route::post('data', [MonthlyControlCountsC3To5Controller::class, 'data']);
                    Route::middleware(['permission:menu_store.inventory_reconciliation.monthly.c3_5.create'])->post('add', [MonthlyControlCountsC3To5Controller::class, 'store']);
                    Route::middleware(['permission:menu_store.inventory_reconciliation.monthly.c3_5.delete'])->delete('delete', [MonthlyControlCountsC3To5Controller::class, 'delete']);
                });
            });
            
            Route::get('{id}/monthly-control-counts/{year}/{month_number}', [ControlCountController::class, 'index']);
            Route::middleware(['permission:menu_store.compliance.monthly_control_counts.create'])->post('{id}/monthly-control-counts/add', [ControlCountController::class, 'store']);
            Route::middleware(['permission:menu_store.compliance.monthly_control_counts.update'])->put('{id}/monthly-control-counts/edit', [ControlCountController::class, 'update']);
            Route::middleware(['permission:menu_store.compliance.monthly_control_counts.delete'])->delete('{id}/monthly-control-counts/delete', [ControlCountController::class, 'delete']);
            Route::post('monthly-control-counts/data', [ControlCountController::class, 'data']);
        });

        /**
         * Marketing
         */
        Route::prefix('marketing')->group(function () {

            // Announcements (StoreAnnouncement)
            Route::get('{id}/announcements', [MarketingStoreAnnouncementController::class, 'index']);
            Route::post('announcements/data', [MarketingStoreAnnouncementController::class, 'data']);
            Route::post('announcements/get/{id}', [MarketingStoreAnnouncementController::class, 'getAnnouncement']);
            Route::get('announcements/view/{id}', [MarketingStoreAnnouncementController::class, 'show']);
            Route::middleware(['permission:menu_store.marketing.announcements.create'])->post('{id}/announcement/add', [MarketingStoreAnnouncementController::class, 'store']);
            Route::middleware(['permission:menu_store.marketing.announcements.update'])->post('{id}/announcement/edit', [MarketingStoreAnnouncementController::class, 'update']);
            Route::middleware(['permission:menu_store.marketing.announcements.delete'])->post('/announcement/delete', [MarketingStoreAnnouncementController::class, 'delete']);

            //News & Events
            Route::get('{id}/news-and-events', [NewsAndEventsController::class, 'index']);
            Route::post('news-and-events/type', [NewsAndEventsController::class, 'getType']);
            Route::get('news-and-events/get-events', [NewsAndEventsController::class, 'getEvents']);
            
            // Route::post('announcements/data', [MarketingStoreAnnouncementController::class, 'data']);
            Route::middleware(['permission:menu_store.marketing.news.create'])->post('{id}/news-and-events/add', [NewsAndEventsController::class, 'store']);
            Route::middleware(['permission:menu_store.marketing.events.create'])->post('{id}/news-and-events/addEvent', [NewsAndEventsController::class, 'storeEvent']);
            Route::middleware(['permission:menu_store.marketing.events.delete'])->delete('news-and-events/delete-event', [NewsAndEventsController::class, 'deleteEvent']);
            Route::middleware(['permission:menu_store.marketing.news.delete'])->delete('news-and-events/delete-news', [NewsAndEventsController::class, 'deleteNews']);

            // Decks
            Route::get('{id}/decks', [DeckController::class, 'index']);
            Route::post('/decks/data', [DeckController::class, 'data']);
            Route::middleware(['permission:menu_store.marketing.decks.create'])
                ->post('{id}/decks/add', [DeckController::class, 'store']);
            Route::middleware(['permission:menu_store.marketing.decks.update'])
                ->post('{id}/decks/edit', [DeckController::class, 'update']);
            Route::middleware(['permission:menu_store.marketing.decks.delete'])
                ->post('{id}/decks/delete', [DeckController::class, 'delete']);

            // References
            Route::get('{id}/references', [ReferenceController::class, 'index']);
            Route::post('/references/data', [ReferenceController::class, 'data']);
            Route::middleware(['permission:menu_store.marketing.references.create'])
                ->post('{id}/references/add', [ReferenceController::class, 'store']);
            Route::middleware(['permission:menu_store.marketing.references.update'])
                ->post('{id}/references/edit', [ReferenceController::class, 'update']);
            Route::middleware(['permission:menu_store.marketing.references.delete'])
                ->post('{id}/references/delete', [ReferenceController::class, 'delete']);
        });

        /**
         *  Forms (jot form)
         */
        Route::prefix('jot-form')->group(function () {
            Route::post('patient-intakes/data', [PatientIntakeController::class, 'data']);
            Route::prefix('{id}/patient-intakes')->group(function () {                
                Route::get('/', [PatientIntakeController::class, 'index']);
                Route::post('sync', [PatientIntakeController::class, 'syncJotForm']);
                Route::get('facesheet/{p_id}', [PatientIntakeController::class, 'facesheet']);
            });
    
            Route::post('release-of-information/data', [ReleaseOfInformationController::class, 'data']);
            Route::prefix('{id}/release-of-information')->group(function () {
                Route::get('/', [ReleaseOfInformationController::class, 'index']);
                Route::post('sync', [ReleaseOfInformationController::class, 'syncJotForm']);
                Route::get('details/{p_id}', [ReleaseOfInformationController::class, 'details']);
            });

            Route::post('patient-prescription-transfers/data', [PatientPrescriptionTransferController::class, 'data']);
            Route::prefix('{id}/patient-prescription-transfers')->group(function () {                
                Route::get('/', [PatientPrescriptionTransferController::class, 'index']);
                Route::post('sync', [PatientPrescriptionTransferController::class, 'syncJotForm']);
                Route::get('facesheet/{p_id}', [PatientPrescriptionTransferController::class, 'facesheet']);
            });
        });

    });

    //PatientController
    Route::get('/admin/divisiontwob/patients', [DivisionTwoBPatientController::class, 'index']);
    Route::post('/admin/divisiontwob/patients/get_data', [DivisionTwoBPatientController::class, 'get_data']); 
    Route::get('/admin/divisiontwob/patients/facesheet/{id}', [DivisionTwoBPatientController::class, 'facesheet']);
    Route::get('/admin/divisiontwob/patients/medications/{id}', [DivisionTwoBPatientController::class, 'get_medications']);
    Route::get('/admin/divisiontwob/patients/allergies/{id}', [DivisionTwoBPatientController::class, 'get_allergies']);
    Route::get('/admin/divisiontwob/patients/demographics/{id}', [DivisionTwoBPatientController::class, 'get_demographics']);
    Route::get('/admin/divisiontwob/patients/notes/{id}', [DivisionTwoBPatientController::class, 'get_notes']);
    Route::get('/admin/divisiontwob/patients/immunization/{id}', [DivisionTwoBPatientController::class, 'get_immunizations']);
    Route::post('/admin/divisiontwob/patients/patient_update', [DivisionTwoBPatientController::class, 'patient_update']); 
    Route::post('/admin/divisiontwob/patients/get_patient_medications_data', [DivisionTwoBPatientController::class, 'get_patient_medications_data']);
    Route::post('/admin/divisiontwob/patients/get_patient_allergies_data', [DivisionTwoBPatientController::class, 'get_patient_allergies_data']);

    Route::post('/admin/divisiontwob/patients/medications_store', [DivisionTwoBPatientController::class, 'medications_store']);
    Route::post('/admin/divisiontwob/patients/medication_delete', [DivisionTwoBPatientController::class, 'medication_destroy']);
    Route::get('/admin/divisiontwob/patients/get_patient_data/{id}', [DivisionTwoBPatientController::class, 'get_patient_data']);
    Route::post('/admin/divisiontwob/patients/medication_update', [DivisionTwoBPatientController::class, 'medication_update']);
    
    Route::post('/admin/divisiontwob/patients/allergy_store', [DivisionTwoBPatientController::class, 'allergy_store']);
    Route::post('/admin/divisiontwob/patients/allergy_delete', [DivisionTwoBPatientController::class, 'allergy_destroy']);
    Route::post('/admin/divisiontwob/patients/allergy_update', [DivisionTwoBPatientController::class, 'allergy_update']);
    Route::post('/admin/divisiontwob/patients/get_patient_notes_data', [DivisionTwoBPatientController::class, 'get_patient_notes_data']);
    Route::post('/admin/divisiontwob/patients/note_store', [DivisionTwoBPatientController::class, 'note_store']);
    Route::get('/admin/divisiontwob/patients/note_download/{id}', [DivisionTwoBPatientController::class, 'note_download']);
    Route::post('/admin/divisiontwob/patients/note_delete', [DivisionTwoBPatientController::class, 'note_destroy']);
    Route::post('/admin/divisiontwob/patients/note_update', [DivisionTwoBPatientController::class, 'note_update']);
    Route::post('/admin/divisiontwob/patients/get_patient_immunizations_data', [DivisionTwoBPatientController::class, 'get_patient_immunizations_data']);
    Route::post('/admin/divisiontwob/patients/immunization_store', [DivisionTwoBPatientController::class, 'immunization_store']);
    Route::post('/admin/divisiontwob/patients/immunization_update', [DivisionTwoBPatientController::class, 'immunization_update']);
    Route::post('/admin/divisiontwob/patients/immunization_delete', [DivisionTwoBPatientController::class, 'immunization_destroy']);

    
     //TebraController
    Route::get('/admin/tebra/get_patient', [TebraController::class, 'get_patient']);
    Route::get('/admin/tebra/get_patients', [TebraController::class, 'get_patients']);
    Route::get('/admin/tebra/get_all_patients', [TebraController::class, 'getAllPatients']);
    Route::get('/admin/tebra/get_everydaypatients', [TebraController::class, 'get_everydayPatients']);
    Route::get('/admin/tebra/get_everydayupdatepatients', [TebraController::class, 'get_everydayUpdatePatientData']);
    Route::get('/admin/tebra/create_patient', [TebraController::class, 'store']);
    Route::get('/admin/tebra/get_appointments', [TebraController::class, 'getAppointments']);
    Route::get('/admin/tebra/get_payments', [TebraController::class, 'getPayments']);
    Route::get('/admin/tebra/history', [TebraController::class, 'history']);

    //MonthlyRevenueController
    // Route::get('/admin/financial_data/monthly_revenue', [MonthlyRevenueController::class, 'index']);
    // Route::post('/admin/financial_data/data', [MonthlyRevenueController::class, 'get_data']);
    // Route::get('/admin/financial_data/get_store_data', [MonthlyRevenueController::class, 'get_stores']);
    // Route::get('/admin/financial_data/get_months_data', [MonthlyRevenueController::class, 'get_months']);
    // Route::post('/admin/financial_data/store', [MonthlyRevenueController::class, 'store']);
    // Route::get('/admin/financial_data/download/{id}', [MonthlyRevenueController::class, 'download']);
    // Route::post('/admin/financial_data/delete', [MonthlyRevenueController::class, 'destroy']);
    // Route::post('/admin/financial_data/update', [MonthlyRevenueController::class, 'update']);
   
   
    Route::prefix('errors')->group(function () {

        Route::get('/403', function () {
            return view('/errors/403/index', [], 403);
        });

        Route::get('/404', function () {
            return view('/errors/404/index', [], 404);
        });
    });

    
});


Route::post('/threerivers_transfer', [OrderController::class, 'threerivers_transfer']);


require __DIR__.'/auth.php';

// Auth::routes();
