<?php

namespace App\Http\Controllers\FinancialReports;

use App\Http\Controllers\Controller;
use App\Models\StoreFolder;
use App\Models\StorePage;
use App\Repositories\FinancialReportRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class DocumentController extends Controller
{
    private FinancialReportRepository $financialReportRepository;

    public function __construct(
        FinancialReportRepository $financialReportRepository
    ) {
        $this->financialReportRepository = $financialReportRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id, $page_id)
    {
        try {

            $pageIds = [
                'all' => array_merge($this->financialReportRepository->pageIds, [$this->financialReportRepository->page_parent_id]),
                'all_eod_reports' => array_merge($this->financialReportRepository->eodReportPageIds, [$this->financialReportRepository->eod_reports_page_id]),
                'all_transaction_receipts' => array_merge($this->financialReportRepository->transactionReceiptPageIds, [$this->financialReportRepository->transaction_receipts_page_id]),
            ];

            if(in_array($page_id, $pageIds['all']))
            {
                $breadCrumb = ['Financial Reports'];
                $allPages = StorePage::query()->FinancialReports()->orderBy('sort', 'asc')->get();
            }

            if(in_array($page_id, $pageIds['all_eod_reports']))
            {
                $breadCrumb = ['Financial Reports', 'EOD Reports'];
                $allPages = StorePage::query()->EODReports()->orderBy('sort', 'asc')->get();
            }

            if(in_array($page_id, $pageIds['all_transaction_receipts']))
            {
                $breadCrumb = ['Financial Reports', 'Transaction Receipts'];
                $allPages = StorePage::query()->TransactionReceipts()->orderBy('sort', 'asc')->get();
            }

            // $page_id = 55;
            $page = StorePage::findOrFail($page_id);
            $code = '';
            $name = '';
            $folders = [];
            if(isset($page->code)) {
                $code = $page->code;
                $name = $page->name;
                $folders = StoreFolder::with('files')->where('page_id', $page_id)->whereNull('parent_id')->get();
                $breadCrumb[] = $name;
            }

            $all = [
                'menu_store.financial_reports.'.$code.'.index',
                'menu_store.financial_reports.'.$code.'.create',
                'menu_store.financial_reports.'.$code.'.update',
                'menu_store.financial_reports.'.$code.'.delete'
            ];
            $permissions = [
                'create' => [$all[1]],
                'update' => [$all[2]],
                'delete' => [$all[3]],
            ];

            $this->checkStorePermission($id, $all); 
 
            $usedFiles = $this->financialReportRepository->getUsedFiles($page_id);

            return view('/stores/financialReports/documents/index', compact('breadCrumb', 'folders', 'usedFiles', 'page', 'allPages','permissions', 'pageIds'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function all($id, $page_id)
    {
        try {

            $pageIds = [
                'all' => array_merge($this->financialReportRepository->pageIds, [$this->financialReportRepository->page_parent_id]),
                'all_eod_reports' => array_merge($this->financialReportRepository->eodReportPageIds, [$this->financialReportRepository->eod_reports_page_id]),
                'all_transaction_receipts' => array_merge($this->financialReportRepository->transactionReceiptPageIds, [$this->financialReportRepository->transaction_receipts_page_id]),
            ];
            
            $index = Permission::where('group_name', 'financial_reports')->where('name','like','%.index')->pluck('name')->all();
            $create = Permission::where('group_name', 'financial_reports')->where('name','like','%.create')->pluck('name')->all();
            $update = Permission::where('group_name', 'financial_reports')->where('name','like','%.update')->pluck('name')->all();
            $delete = Permission::where('group_name', 'financial_reports')->where('name','like','%.delete')->pluck('name')->all();
            $all = array_merge($index, $create, $update, $delete);
            
            $permissions = [
                'create' => $create,
                'update' => $update,
                'delete' => $delete,
            ];
            $this->checkStorePermission($id, $all); 


            $call = 'FinancialReports';
            $breadCrumb = ['Financial Reports', 'All Files'];
            if($page_id == $this->financialReportRepository->eod_reports_page_id)
            {
                $call = 'EODReports';
                $breadCrumb = ['Financial Reports', 'EOD Reports', 'All Files'];
            }
            if($page_id == $this->financialReportRepository->transaction_receipts_page_id)
            {
                $call = 'TransactionReceipts';
                $breadCrumb = ['Financial Reports', 'Transaction Receipts', 'All Files'];
            }
            
            $folders = StoreFolder::with('files')->$call()->get();
            $allPages = StorePage::query()->$call()->orderBy('sort', 'asc')->get();
            $usedFiles = $this->financialReportRepository->getUsedFiles($page_id);

            $filesCounting = $this->financialReportRepository->getFilesCountPerPage($page_id);

            
            return view('/stores/financialReports/allFiles/index', compact('breadCrumb', 'folders', 'usedFiles', 'allPages','permissions', 'filesCounting', 'pageIds'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            $data = $this->financialReportRepository->getDataTable($request);
            return response()->json($data, 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($id, Request $request)
    {
        if($request->ajax()){
            DB::beginTransaction();
            try {

                $data = $this->financialReportRepository->store($request);
                DB::commit();

                return json_encode([
                    'data'=> $data,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in DocumentController.store.db_transaction.'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $this->financialReportRepository->delete($request->id);

                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in DocumentController.delete.'
                ]);
            }
        }
    }

}
