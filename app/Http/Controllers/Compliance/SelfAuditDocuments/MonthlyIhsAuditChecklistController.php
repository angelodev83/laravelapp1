<?php

namespace App\Http\Controllers\Compliance\SelfAuditDocuments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Interfaces\ISelfAuditDocumentRepository;

class MonthlyIhsAuditChecklistController extends Controller
{
    private ISelfAuditDocumentRepository $repository;

    public function __construct(ISelfAuditDocumentRepository $repository) {
        $this->repository = $repository;

        $this->middleware('permission:menu_store.cnr.self_audit_documents.m_ihs_a_c.index|menu_store.cnr.self_audit_documents.m_ihs_a_c.create|menu_store.cnr.self_audit_documents.m_ihs_a_c.update|menu_store.cnr.self_audit_documents.m_ihs_a_c.delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);
            
            $breadCrumb = ['Compliance & Regulation', 'Self-Audit Documents', 'Monthly IHS Audit Checklist'];
            return view('/stores/compliance/selfAuditDocuments/monthlyIhsAuditChecklist/index', compact('breadCrumb'));
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
            try{
                DB::beginTransaction();
                try {

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
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in MonthlyIhsAuditChecklistController.store.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in MonthlyIhsAuditChecklistController.store.'
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
                    'message' => 'Something went wrong in ComplianceAuditController.delete.'
                ]);
            }
        }
    }


}
