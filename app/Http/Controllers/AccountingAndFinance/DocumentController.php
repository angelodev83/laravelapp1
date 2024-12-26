<?php

namespace App\Http\Controllers\AccountingAndFinance;

use App\Http\Controllers\Controller;
use App\Models\StoreFolder;
use App\Models\StorePage;
use App\Repositories\AccountingAndFinanceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class DocumentController extends Controller
{
    private AccountingAndFinanceRepository $accountingAndFinanceRepository;

    public function __construct(
        AccountingAndFinanceRepository $accountingAndFinanceRepository
    ) {
        $this->accountingAndFinanceRepository = $accountingAndFinanceRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($page_id)
    {
        try {

            $pageIds = [
                'all' => array_merge($this->accountingAndFinanceRepository->pageIds, [$this->accountingAndFinanceRepository->page_parent_id])
            ];

            if(in_array($page_id, $pageIds['all']))
            {
                $breadCrumb = ['Accounting and Finance'];
                $allPages = StorePage::query()->AccountingAndFinance()->orderBy('sort', 'asc')->get();
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
                'accounting_and_finance.'.$code.'.index',
                'accounting_and_finance.'.$code.'.create',
                'accounting_and_finance.'.$code.'.update',
                'accounting_and_finance.'.$code.'.delete'
            ];
            $permissions = [
                'create' => [$all[1]],
                'update' => [$all[2]],
                'delete' => [$all[3]],
            ]; 

            $this->checkHasAnyPermission($all);
 
            $usedFiles = $this->accountingAndFinanceRepository->getUsedFiles($page_id);

            return view('/accountingAndFinance/documents/index', compact('breadCrumb', 'folders', 'usedFiles', 'page', 'allPages','permissions', 'pageIds'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function all($page_id)
    {
        try {

            $pageIds = [
                'all' => array_merge($this->accountingAndFinanceRepository->pageIds, [$this->accountingAndFinanceRepository->page_parent_id])
            ];
            
            $index = Permission::where('group_name', 'accounting_and_finance')->where('name','like','%.index')->pluck('name')->all();
            $create = Permission::where('group_name', 'accounting_and_finance')->where('name','like','%.create')->pluck('name')->all();
            $update = Permission::where('group_name', 'accounting_and_finance')->where('name','like','%.update')->pluck('name')->all();
            $delete = Permission::where('group_name', 'accounting_and_finance')->where('name','like','%.delete')->pluck('name')->all();
            $all = array_merge($index, $create, $update, $delete);

            $this->checkHasAnyPermission($all);
            
            $permissions = [
                'create' => $create,
                'update' => $update,
                'delete' => $delete,
            ];

            $call = 'AccountingAndFinance';
            $breadCrumb = ['Accounting And Finance', 'All Files'];

            $folders = StoreFolder::with('files')->$call()->get();
            $allPages = StorePage::query()->$call()->orderBy('sort', 'asc')->get();
            $usedFiles = $this->accountingAndFinanceRepository->getUsedFiles($page_id);

            $filesCounting = $this->accountingAndFinanceRepository->getFilesCountPerPage($page_id);

            
            return view('/accountingAndFinance/allFiles/index', compact('breadCrumb', 'folders', 'usedFiles', 'allPages','permissions', 'filesCounting', 'pageIds'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            $data = $this->accountingAndFinanceRepository->getDataTable($request);
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

                $data = $this->accountingAndFinanceRepository->store($request);
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

                $this->accountingAndFinanceRepository->delete($request->id);

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
