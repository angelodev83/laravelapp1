<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StoreFile;
use App\Models\StoreFolder;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

ini_set('max_execution_time', '3600');

class StoreFolderController extends Controller
{
    public function update(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();

                $folder = StoreFolder::findOrFail($request->id);

                if(isset($folder->id)) {
                    $folder->name = $request->name;
                    $folder->background_color = $request->background_color;
                    $folder->text_color = $request->text_color;
                    $folder->border_color = $request->border_color;
                    $folder->icon_path = $request->icon_path;
                    $save = $folder->save();
                }
                
                DB::commit();
                
                return json_encode([
                    'data'=> $folder,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in StoreFolderController.update.'
            ]);
        }
    }

    public function delete(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();

                $folder_id = $request->id;
                $pharmacy_store_id = $request->pharmacy_store_id ?? null;

                $page_id = null;

                $folder = StoreFolder::with('page')->findOrFail($folder_id);

                $logs = [
                    'store_folders' => $folder,
                    'store_files' => []
                ];

                $save = false;

                if(isset($folder->id)) {
                    $files = StoreFile::where('folder_id', $folder_id)
                        ->where('pharmacy_store_id', $pharmacy_store_id)
                        ->get();
                    
                    $logs['store_files'] = $files->toArray();

                    $page_code = $folder->page->code;
                    $page_id = $folder->page->id;
                    
                    $aws_s3_path = env('AWS_S3_PATH');
                    $path = "$aws_s3_path/stores/$pharmacy_store_id/knowledge-base/$page_code/$folder_id/";
                    
                    Storage::disk('s3')->deleteDirectory($path);

                    $save = StoreFile::where('folder_id', $folder_id)
                        ->where('pharmacy_store_id', $pharmacy_store_id)
                        ->delete();

                    $save = $folder->delete();
                }

                if($save) {
                    //delete history
                    $transactionLog = new TransactionLog();
                    $transactionLog->user_id = auth()->user()->id;
                    $transactionLog->pharmacy_store_id = $pharmacy_store_id;
                    $transactionLog->page_id = $page_id;
                    $transactionLog->module_name = 'store_folders';
                    $transactionLog->module_id = $folder_id;
                    $transactionLog->function = 'StoreFolderController.delete';
                    $transactionLog->action = 'deleted';
                    $transactionLog->subject = 'User ID: '.auth()->user()->id . ', Username: '.auth()->user()->name . ' DELETED Folder ID: '.$folder_id.', Folder Name: '.$logs['store_folders']['name'] .' and ('.count($logs['store_files']).') File records inside the folder';
                    $transactionLog->data = json_encode($logs);
                    $transactionLog->save();
                }

                DB::commit();

                return json_encode([
                    'data'=> $save,
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in StoreFolderController.delete.'
                ]);
            }
        }
    }
}
