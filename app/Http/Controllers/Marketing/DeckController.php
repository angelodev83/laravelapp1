<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Icon;
use App\Models\StoreFolder;
use App\Repositories\StoreFolderFileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeckController extends Controller
{
    private StoreFolderFileRepository $folderFileRepository;

    public function __construct(
        StoreFolderFileRepository $folderFileRepository
    ) {
        $this->folderFileRepository = $folderFileRepository;

        $this->middleware('permission:menu_store.marketing.decks.index|menu_store.marketing.decks.create|menu_store.marketing.decks.update|menu_store.marketing.decks.delete');
    }

    public function index($id)
    {
        try {
            $this->checkStorePermission($id);      
            
            $icons = Icon::all();
            $page_id = 78;
            $permissions = [
                'create' => ['menu_store.marketing.decks.create'],
                'update' => ['menu_store.marketing.decks.update'],
                'delete' => ['menu_store.marketing.decks.delete'],
            ];
            $folders = StoreFolder::with('files')->where('page_id', 78)->get();

            $breadCrumb = ['Marketing', 'Decks'];
            return view('/stores/marketing/decks/index', compact('breadCrumb', 'folders', 'page_id', 'permissions', 'icons'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            $permissions = [
                'permissions' => [
                    'prefix' => 'menu_store.marketing.',
                    'delete' => ['menu_store.marketing.decks.delete'],
                ]
            ];
            $request->merge($permissions);
            $data = $this->folderFileRepository->getDataTable($request);
            return response()->json($data, 200);
        }
    }

    public function store($id, Request $request)
    {
        if($request->ajax()){
            DB::beginTransaction();
            try {

                $data = $this->folderFileRepository->store($request);

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
                    'message' => 'Something went wrong in SOPController.store.db_transaction.'
                ]);
            }
        }
    }

    public function delete(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $this->folderFileRepository->delete($request->id);

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
                    'message' => 'Something went wrong in DeckController.delete.'
                ]);
            }
        }
    }

    public function deleteFolder(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $this->folderFileRepository->deleteFolder($request);

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
                    'message' => 'Something went wrong in DeckController.delete.'
                ]);
            }
        }
    }

}
