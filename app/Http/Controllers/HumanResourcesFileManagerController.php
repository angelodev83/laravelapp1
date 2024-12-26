<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Icon;
use App\Models\StoreFolder;
use App\Repositories\StoreFolderFileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HumanResourcesFileManagerController extends Controller
{
    private StoreFolderFileRepository $folderFileRepository;

    public function __construct(
        StoreFolderFileRepository $folderFileRepository
    ) {
        $this->folderFileRepository = $folderFileRepository;

        $this->middleware('permission:hr.file_manager.index|hr.file_manager.create|hr.file_manager.update|hr.file_manager.delete');
    }

    public function index()
    {
        try {            
            $icons = Icon::all();
            $page_id = 90;
            $permissions = [
                'create' => ['hr.file_manager.create'],
                'update' => ['hr.file_manager.update'],
                'delete' => ['hr.file_manager.delete'],
            ];
            $folders = StoreFolder::with('files')->where('page_id', 90)->get();

            $breadCrumb = ['Human Resources', 'File Manager'];
            return view('/humanResources/fileManager/index', compact('breadCrumb', 'folders', 'page_id', 'permissions', 'icons'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            $permissions = [
                'permissions' => [
                    'prefix' => 'hr.',
                    'delete' => ['hr.file_manager.delete'],
                ]
            ];
            $request->merge($permissions);
            $data = $this->folderFileRepository->getDataTable($request);
            return response()->json($data, 200);
        }
    }

    public function store(Request $request)
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
                    'message' => 'Something went wrong in HumanResourcesFileManagerController.store.db_transaction.'
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
                    'message' => 'Something went wrong in HumanResourcesFileManagerController.delete.'
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
                    'message' => 'Something went wrong in HumanResourcesFileManagerController.delete.'
                ]);
            }
        }
    }
}
