<?php

namespace App\Http\Controllers\PharmacyStoreAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\StoreDocument;
use App\Http\Controllers\Controller;
use App\Interfaces\ITaskRepository;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class StoreDocumentController extends Controller
{
    private ITaskRepository $repository;

    private $document;

    public function __construct(
        StoreDocument $document
        ,   ITaskRepository $repository
    ) {
        $this->document = $document;
        $this->repository = $repository;

        // $this->middleware('permission:menu_store.escalation.tickets.index|menu_store.escalation.tickets.create|menu_store.escalation.tickets.update|menu_store.escalation.tickets.delete');
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            
            $this->repository->setDocumentDataTable($request);
            $data = $this->repository->getDocumentDataTable();
            
            return response()->json($data, 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {

                    $this->repository->storeDocument($request);
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
                        'message' => 'Something went wrong in StoreDocumentController.store.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in StoreDocumentController.store.'
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

                // $this->repository->deleteDocument($request->id);

                $document = StoreDocument::findOrFail($request->id);

                $category = $document->category;

                $path = $document->path.$document->name;

                $save =  false;
                if($path != ''){
                    if(Storage::disk('s3')->exists($path)) {
                        Storage::disk('s3')->delete($path);
                    }
                    @unlink(public_path('/'.$document->path));
                    $save = $document->delete();   

                    switch($category) {
                        case 'task':
                            DB::table('task_comment_documents')->where('document_id', $request->id)->delete();
                            break;
                        case 'ticket':
                            DB::table('ticket_comment_documents')->where('document_id', $request->id)->delete();
                            break;
                    }
                }


                DB::commit();

                return json_encode([
                    'data'=> $document,
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in StoreDocumentController.delete.'
                ]);
            }
        }
    }

    public function downloadS3($id)
    {
        $file = StoreDocument::findOrFail($id);
        
        $headers = [
            'Content-Type'        => 'Content-Type: '.$file->mime_type.' ',
            'Content-Disposition' => 'attachment; filename="'. $file->name .'"',
        ];
        
        $path = $file->path.$file->name;
        
        return Response::make(Storage::disk('s3')->get($path), 200, $headers);
    }

}
