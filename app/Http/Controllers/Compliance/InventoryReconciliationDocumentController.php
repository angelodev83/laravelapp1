<?php

namespace App\Http\Controllers\Compliance;

use App\Models\InventoryReconciliationDocument;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Interfaces\IInventoryReconciliationDocumentRepository;

class InventoryReconciliationDocumentController extends Controller
{
    private $document;
    private IInventoryReconciliationDocumentRepository $repository;

    public function __construct(
        InventoryReconciliationDocument $document
        ,   IInventoryReconciliationDocumentRepository $repository
    ) {
        $this->document = $document;
        $this->repository = $repository;

        $this->middleware('permission:menu_store.cnr.inventory_reconciliation.index|menu_store.cnr.inventory_reconciliation.create|menu_store.cnr.inventory_reconciliation.update|menu_store.cnr.inventory_reconciliation.delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Compliance & Regulation', 'Inventory Reconciliation'];
            return view('/stores/compliance/inventoryReconciliation/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function data(Request $request)
    {
        if($request->ajax()){
            
            $this->repository->setDataTable($request);
            $data = $this->repository->getDataTable();
            
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

                $this->repository->store($request, $id);
                DB::commit();

                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in InventoryReconciliationDocumentController.store.db_transaction.'
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryReconciliationDocument $inventoryReconciliationDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryReconciliationDocument $inventoryReconciliationDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryReconciliationDocument $inventoryReconciliationDocument)
    {
        //
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
                    'message' => 'Something went wrong in InventoryReconciliationDocumentController.delete.'
                ]);
            }
        }
    }
}
