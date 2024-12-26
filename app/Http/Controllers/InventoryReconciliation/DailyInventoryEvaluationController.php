<?php

namespace App\Http\Controllers\InventoryReconciliation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Interfaces\IInventoryReconciliationDocumentRepository;

class DailyInventoryEvaluationController extends Controller
{
    private IInventoryReconciliationDocumentRepository $repository;

    public function __construct(IInventoryReconciliationDocumentRepository $repository) {
        $this->repository = $repository;

        $this->middleware('permission:menu_store.inventory_reconciliation.daily.index|menu_store.inventory_reconciliation.daily.create|menu_store.inventory_reconciliation.daily.delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);
            
            $breadCrumb = ['Inventory Reconciliation', 'Daily Inventory Evaluation'];
            return view('/stores/inventoryReconciliation/dailyInventoryEvaluation/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            
            $this->repository->setDataTable($request, 0);
            $data = $this->repository->getDataTable();
            
            return response()->json($data, 200);
        }
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();

                $this->repository->store($request);
                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'status' => $e->getCode(),
                    'error' => 'warning',
                    'message' => $e->getMessage()
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
                
                $this->repository->delete($request->id);

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
                    'message' => 'Something went wrong in DailyInventoryEvaluationController.delete.'
                ]);
            }
        }
    }
    
}
