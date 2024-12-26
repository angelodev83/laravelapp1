<?php

namespace App\Http\Controllers\Escalation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Interfaces\ITicketRepository;

class TicketDocumentController extends Controller
{
    private ITicketRepository $repository;

    public function __construct(ITicketRepository $repository
    ) {
        $this->repository = $repository;

        $this->middleware('permission:menu_store.escalation.tickets.index|menu_store.escalation.tickets.create|menu_store.escalation.tickets.update|menu_store.escalation.tickets.delete');
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
                        'message' => 'Something went wrong in TicketDocumentController.store.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in TicketDocumentController.store.'
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

                $this->repository->deleteDocument($request->id);

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
                    'message' => 'Something went wrong in TicketDocumentController.delete.'
                ]);
            }
        }
    }

}
