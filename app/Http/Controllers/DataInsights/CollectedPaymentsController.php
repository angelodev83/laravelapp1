<?php

namespace App\Http\Controllers\DataInsights;

use App\Http\Controllers\Controller;
use App\Interfaces\UploadInterface;
use App\Models\CollectedPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CollectedPaymentsController extends Controller
{   
    private UploadInterface $repository;

    public function __construct(UploadInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware('permission:menu_store.data_insights.collected_payments.index');
    }

    public function index($id)
    {
        try{
            $this->checkStorePermission($id);
            
            $user = Auth::user();

            $breadCrumb = ['Data Insights', 'Collected Payments'];
            
            return view('/stores/dataInsights/collectedPayments/index', compact('user','breadCrumb'));
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
                $this->repository->uploadCollectedPayments($request);
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
                'message' => 'Something went wrong in OperationOrderController.upload.'
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
                'paid_amount' => 'numeric|decimal:0,2',
            ]);

            if ($validation->passes()){

                $cp = CollectedPayment::where('id', $dataArray['id'])->first();
                $cp->account_name = ($dataArray['account_name'] == '')?'':$dataArray['account_name'];
                // $cp->payment_date = ($dataArray['payment_date'] == '')?'':$dataArray['payment_date'];
                $cp->paid_amount = ($dataArray['paid_amount'] == '')?'':$dataArray['paid_amount'];
                // $cp->running_balance_as_of_date = ($dataArray['running_balance_as_of_date'] == '')?null:$dataArray['running_balance_as_of_date'];
                $cp->save();
                
                return json_encode([
                    'data' => $cp,
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
            $cp = CollectedPayment::where('id', $id)->first();
            $cp->delete();

            return json_encode(['status'=>'success','message'=>'Record has been deleted.']);
        }
    }

    public function get_data(Request $request)
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
            $query = CollectedPayment::with('user.employee')->where('pharmacy_store_id', $request->pharmacy_store_id);

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
                // if(Auth::user()->can('menu_store.eod_register_report.register.index')) {
                //     $actions .= '<a title="View" href="javascript:void(0)" data-id="'.$value->id.'" 
                //                 data-array="'.htmlspecialchars(json_encode($value)).'" class="me-1"
                //                 id="inmar-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showViewForm('.$value->id.');"><i class="fa fa-eye"></i></button></a>';
                // }
                // if(Auth::user()->can('menu_store.eod_register_report.register.update')) {
                //     $actions .= '<a title="Files" href="javascript:void(0)" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"  class="me-1"
                //                 id="data-file-btn-'.$value->id.'"><button class="btn btn-sm btn-secondary" onclick="showFileModal('.$value->id.');"><i style="padding-left: 2px; padding-right: 2px;" class="fa-solid fa-file"></i></button></a>';
                // } 
                if(Auth::user()->can('menu_store.data_insights.collected_payments.update')) {
                    $actions .= '<a title="Edit" href="javascript:void(0)" data-id="'.$value->id.'" data-array="'.htmlspecialchars(json_encode($value)).'"  class="me-1"
                                id="data-show-btn-'.$value->id.'"><button class="btn btn-sm btn-primary" onclick="showEditForm('.$value->id.');"><i class="fa fa-pencil"></i></button></a>';
                }
                if(Auth::user()->can('menu_store.data_insights.collected_payments.delete')) {
                    $actions .= '<a title="Delete" href="javascript:void(0)" class="me-1"><button class="btn btn-sm btn-danger" onclick="ShowConfirmDeleteForm(' . $value->id . ')"><i class="fa fa-trash-can"></i></button></a>';
                }             
                $actions .= '</div>';
                
                $newData[] = [
                    'id' => $value->id,
                    'account_name' => $value->account_name,
                    'reconciling_account_name' => $value->reconciling_account_name,
                    'account_number' => $value->account_number,
                    // 'payment_date' => $value->payment_date,
                    'pos_sales_date' => $value->pos_sales_date,
                    'f_pos_sales_date' => date('F d, Y', strtotime($value->pos_sales_date)),
                    'posting_of_payment_date' => $value->posting_of_payment_date,
                    'f_posting_of_payment_date' => date('F d, Y', strtotime($value->posting_of_payment_date)),
                    'paid_amount' => $value->paid_amount,
                    'rx_number' => $value->rx_number,
                    // 'running_balance_as_of_date' => $value->running_balance_as_of_date,
                    'user' => $value->user->employee->firstname.' '.$value->user->employee->lastname,
                    'created_at' => date('M d, Y g:i A', strtotime($value->pst_created_at)),
                    'actions' =>  $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }
}
