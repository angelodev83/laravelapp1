<?php

namespace App\Http\Controllers\DataInsights;

use App\Http\Controllers\Controller;
use App\Interfaces\UploadInterface;
use App\Models\AccountReceivable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountReceivablesController extends Controller
{   
    private UploadInterface $repository;

    public function __construct(UploadInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware('permission:menu_store.data_insights.account_receivables.index|menu_store.data_insights.account_receivables.create|menu_store.data_insights.account_receivables.update|menu_store.data_insights.account_receivables.delete');
    }

    public function index($id)
    {
        try{
            $this->checkStorePermission($id);
            
            $user = Auth::user();

            $breadCrumb = ['Data Insights', 'Account Receivables'];
            
            return view('/stores/dataInsights/accountReceivables/index', compact('user','breadCrumb'));
        }
        catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function upload(Request $request)
    {
        try {
            if($request->ajax()){
                DB::beginTransaction();
                $this->repository->uploadAccountReceivables($request);
                DB::commit();
                
                return json_encode([
                    'data'=> [],
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in AccountReceivablesController.upload.'
            ]);
        }
    }

    public function update(Request $request)
    {
        if($request->ajax()){
            
            $input = $request->all();
            
            $dataArray = json_decode($input['data'], true);
            
            $validation = Validator::make($dataArray, [
                'account_name' => 'required',
                'amount_total_balance' => 'numeric|decimal:0,2',
            ]);

            if ($validation->passes()){

                $ar = AccountReceivable::where('id', $dataArray['id'])->first();

                foreach($dataArray as $key => $value) {
                    if($key != 'id') {
                        $ar->$key = $value;
                    }
                }
                $ar->save();
                
                return json_encode([
                    'status'=> 'success',
                    'message'=> 'Record has been updated.'
                ]);
            } else{
                return json_encode([
                    'status'=>'error',
                    'errors'=> $validation->errors(),
                    'message'=>'Record saving failed.'
                ]);
            }

        }
    } 

    public function destroy(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();

            $id = $input['id'];
            $cp = AccountReceivable::where('id', $id)->first();
            $cp->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function data(Request $request)
    {   
        if($request->ajax()){
            // Page Length
            $pageNumber = ( $request->start / $request->length )+1;
            $pageLength = $request->length;
            $skip       = ($pageNumber-1) * $pageLength;

            // Page Order
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';

            // get data from products table
            // $query = DB::table('view_inmars')->where('pharmacy_store_id', $id)->groupBy('name');
            $query = AccountReceivable::with('user.employee')->where('pharmacy_store_id', $request->pharmacy_store_id);

            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
                $query->orWhereHas('user.employee', function ($query) use ($search) {
                        $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $search . '%']);
                    });
            });

            $orderByCol =  $columns[$orderColumnIndex]['name'];

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();
            
            $newData = [];
            foreach ($data as $value) {
                

                $actions = '<div class="d-flex order-actions">';
                if(Auth::user()->can('menu_store.data_insights.account_receivables.update')) {
                    $actions .= '<a title="Edit" href="javascript:void(0)" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"  class="me-1"
                                id="data-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.data_insights.account_receivables.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button></a>';
                }             
                $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->id,
                    'account_name' => $value->account_name,
                    'account_number' => $value->account_number,
                    'amount_last_payment' => $value->amount_last_payment,
                    'amount_unreconciled' => $value->amount_unreconciled,
                    'amount_total_balance' => $value->amount_total_balance,
                    'as_of_date' => $value->as_of_date,
                    'formatted_as_of_date' => date('M d, Y', strtotime($value->as_of_date)),
                    'formatted_amount_last_payment' => number_format($value->amount_last_payment, 2, '.', ','),
                    'formatted_amount_unreconciled' => number_format($value->amount_unreconciled, 2, '.', ','),
                    'formatted_amount_total_balance' => number_format($value->amount_total_balance, 2, '.', ','),
                    'created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'created_by' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }
}
