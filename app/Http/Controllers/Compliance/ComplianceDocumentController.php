<?php

namespace App\Http\Controllers\Compliance;

use App\Models\ComplianceDocument;
use App\Models\PharmacyStore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use File;

use App\Interfaces\IDocumentRepository;

class ComplianceDocumentController extends Controller
{
    private $document;
    private IDocumentRepository $repository;

    public function __construct(
        ComplianceDocument $document
        ,   IDocumentRepository $repository
    ) {
        $this->document = $document;
        $this->repository = $repository;

        $this->middleware('permission:menu_store.cnr.documents.index|menu_store.cnr.documents.create|menu_store.cnr.documents.update|menu_store.cnr.documents.delete');
    }
    

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Compliance & Regulation', 'Documents'];
            return view('/stores/compliance/documents/index', compact('breadCrumb'));
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

    /**
     * Store a newly created resource in storage.
     */
    public function store($id, Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {

                    $this->repository->store($request, $id, 0);
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
                        'message' => 'Something went wrong in ComplianceDocumentController.store.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in ComplianceDocumentController.store.'
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ComplianceDocument $complianceDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ComplianceDocument $complianceDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ComplianceDocument $complianceDocument)
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
                    'message' => 'Something went wrong in ComplianceDocumentController.delete.'
                ]);
            }
        }
    }
}
