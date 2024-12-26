<?php

namespace App\Http\Controllers\KnowledgeBase;

use App\Http\Controllers\Controller;
use App\Models\Icon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\StoreFolder;
use App\Repositories\KnowledgeBaseRepository;

class ProcessDocumentsController extends Controller
{
    private KnowledgeBaseRepository $knowledgeBaseRepository;

    public function __construct(
        KnowledgeBaseRepository $knowledgeBaseRepository
    ) {
        $this->knowledgeBaseRepository = $knowledgeBaseRepository;

        $this->middleware('permission:menu_store.knowledge_base.pd.index|menu_store.knowledge_base.pd.create|menu_store.knowledge_base.pd.update|menu_store.knowledge_base.pd.delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);      
            
            $icons = Icon::where('store_page_id', 34)->get();
            $page_id = 37;
            $knowledgeBasePermissions = [
                'create' => ['menu_store.knowledge_base.pd.create'],
                'update' => ['menu_store.knowledge_base.pd.update'],
                'delete' => ['menu_store.knowledge_base.pd.delete'],
            ];
            $folders = StoreFolder::with('files')->ProcessDocuments()->get();
            $usedFiles = $this->knowledgeBaseRepository->getUsedFiles($page_id);

            $breadCrumb = ['Knowledge Base', 'Process Documents'];
            return view('/stores/knowledgeBase/processDocuments/index', compact('breadCrumb', 'folders', 'usedFiles', 'page_id', 'knowledgeBasePermissions', 'icons'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            $data = $this->knowledgeBaseRepository->getDataTable($request);
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

                $data = $this->knowledgeBaseRepository->store($request);
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
                    'message' => 'Something went wrong in ProcessDocumentsController.store.db_transaction.'
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

                $this->knowledgeBaseRepository->delete($request->id);

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
                    'message' => 'Something went wrong in ProcessDocumentsController.delete.'
                ]);
            }
        }
    }

}
