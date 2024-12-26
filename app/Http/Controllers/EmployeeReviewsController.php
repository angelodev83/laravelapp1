<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Icon;
use App\Models\StoreFile;
use App\Models\StoreFolder;
use App\Repositories\StoreFolderFileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeReviewsController extends Controller
{
    private StoreFolderFileRepository $folderFileRepository;

    public function __construct(
        StoreFolderFileRepository $folderFileRepository
    ) {
        $this->folderFileRepository = $folderFileRepository;

        $this->middleware('permission:hr.employee_reviews.index|hr.employee_reviews.create|hr.employee_reviews.update|hr.employee_reviews.delete');
    }

    public function index($year, $month_number)
    {
        try {
            
            $icons = Icon::select(DB::raw('DISTINCT name'),'id', 'path', 'store_page_id')->get();
            $page_id = 80;
            $permissions = [
                'create' => ['hr.employee_reviews.create'],
                'update' => ['hr.employee_reviews.update'],
                'delete' => ['hr.employee_reviews.delete'],
            ];
            $folders = StoreFolder::with('files.tag')
                ->where('page_id', 80)->get();

            $monthlyCountFolders = [];
            foreach($folders as $f) {
                $count = $f->files->count();
                if(!empty($year)) {
                    $count = StoreFile::with('tag')->where('folder_id', $f->id)->whereHas('tag', function($q) use ($year, $month_number) {
                        $q->where('year', $year);
                        if(!empty($month_number)) {
                            $q->where("month", $month_number);
                        }
                    })->count();
                }
                $monthlyCountFolders[$f->id] = $count;
            }

            $weeks = [];

            $breadCrumb = ['Human Resource', 'Employee Reviews'];
            return view('/humanResources/employeeReviews/index', compact('breadCrumb', 'folders', 'page_id', 'permissions', 'icons', 'monthlyCountFolders', 'weeks'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            $permissions = [
                'permissions' => [
                    'prefix' => 'menu_store.operations.',
                    'delete' => ['hr.employee_reviews.delete'],
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
                    'message' => 'Something went wrong in MeetingController.delete.'
                ]);
            }
        }
    }
}
