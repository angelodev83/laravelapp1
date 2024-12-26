<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\OperationReturn;
use App\Models\Patient;
use App\Models\ReturnItem;
use App\Models\StoreStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OperationReturnController extends Controller
{
    public function index($id)
    {
        try {
            $this->checkStorePermission($id);

            $breadCrumb = ['Operations', 'Return to Stock'];
            
            return view('/stores/operations/rts/index', compact('breadCrumb'));
        } catch (\Throwable $th) {
            return response()->view('/errors/403/index', [], 403);
        }
    }

    public function get_status(Request $request)
    {
        if($request->ajax()){

            $statuses = StoreStatus::where('category', 'return')->get()->toArray();

            return response()->json([
                "statuses" => $statuses,
            ], 200);
        }

    }

    public function get_medications(Request $request)
    {
        if($request->ajax()){
            $data = Medication::select('med_id', 'name')->where('name', 'like', "%".$request->term."%")->take(20)->get();

            return json_encode([
                'data'=> $data,
            ]);
        }

    }

    public function get_patients(Request $request)
    {
        if($request->ajax()){
           $data = Patient::select('id','firstname','lastname')
            ->where(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%".$request->term."%")
            ->where('source', 'pioneer')
            ->take(20)
            ->get();


            return json_encode([
                'data'=> $data,
            ]);
        }
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            $data = [];
            $input = $request->all();

            $validation = Validator::make($input, [
                // 'patient_id' => 'required',
                'date' => 'required',
            ]);

            if ($validation->passes()){

                $operation_return = new OperationReturn();
                // $operation_return->patient_id = $input["patient_id"];
                $operation_return->reason = $input["reason"];
                $operation_return->date = $input["date"];
                $operation_return->pharmacy_store_id = $input['pharmacy_store_id'];
                $operation_return->user_id = auth()->user()->id;
                $operation_return->save();

                // Create Item entries
                $check_entry = 0;
                for ($i = 0; $i <= $input['med_count']; $i++) {
                    if (!empty($request->input("drugname$i")&&$request->input("quantity$i"))) {
                        // $data['medications'][]=array(
                        //     "drugname" => $input["drugname$i"],
                        //     "strength" => $input["strength$i"],
                        // );
                        $return_item = new ReturnItem();
                        $return_item->operation_return_id = $operation_return->id;
                        $return_item->med_id = $input["drugname$i"];
                        $return_item->quantity = $input["quantity$i"];
                        $return_item->rx_number = $input["rx_number$i"];
                        $return_item->save();

                        $check_entry = 1;
                    }
                }
                //return no entry of medication
                if($check_entry === 0)
                {
                    $medication_validate = [
                        "medication_holder" => ["Input at least one medication field."],
                        "message" => "Record saving failed."
                    ];

                    return json_encode([
                        'status'=>'error',
                        'errors'=> $medication_validate,
                        'message'=>'Record saving failed.'
                    ], 422);
                }

                return json_encode([
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ], 201);
            }
            else{
                return json_encode([
                    'status'=>'warning',
                    'errors'=> $validation->errors(),
                    'message'=>'Check input fields.'
                ], 422);
            }
        }
    }

    public function update(Request $request)
    {
        if($request->ajax()){
            $input = $request->all();

            $validation = Validator::make($input, [
                // 'patient_id' => 'required',
                'date' => 'required',
            ]);

            if ($validation->passes()){
                $return_item = ReturnItem::where('id', $input["id"])->first();
                $return_item->med_id = $input["drugname"];
                $return_item->quantity = $input["quantity"];
                $return_item->rx_number = $input["rx_number"]; 

                $operation_return = OperationReturn::where('id', $return_item->operation_return_id)->first();
                // $operation_return->patient_id = $input["patient_id"];
                $operation_return->reason = $input["reason"];
                $operation_return->date = $input["date"];
                $operation_return->status_id = $input["status_id"];
                $operation_return->user_id = auth()->user()->id;
                $operation_return->save();
                $return_item->save();
 
                return json_encode([
                    'status'=>'success',
                    'message'=>'Record has been updated.'
                ], 200);
            }
            else{
                return json_encode([
                    'status'=>'warning',
                    'errors'=> $validation->errors(),
                    'message'=>'Check input fields.'
                ], 422);
            }
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            $return_item_id = $request->input('id');

            $return_item = ReturnItem::where('id', $return_item_id)->first();
            $operation_return_id = $return_item->operation_return_id;
            $count = ReturnItem::where('operation_return_id', $operation_return_id)->count();
            if($count === 1){
                $operation_return = OperationReturn::where('id', $operation_return_id)->first();
                $operation_return->delete();
            }
            $return_item->delete();

            return response()->json([
                "status" => 'success',
                "message" => 'Record has been deleted.',
            ], 200);
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
            $query = OperationReturn::select(
                // DB::raw('CONCAT(patients.firstname, " ", patients.lastname) AS patient_name'),
                // 'patients.id AS patient_id',
                'store_statuses.name AS status_name',
                'store_statuses.class AS status_class',
                'store_statuses.id AS status_id',
                'operation_returns.reason',
                'operation_returns.user_id',
                'return_items.id AS id',
                'medications.med_id AS med_id',
                'medications.name AS med_name',
                'return_items.quantity',
                'return_items.id AS return_item_id',
                'return_items.rx_number',
                'operation_returns.date'
            )
                // ->join('patients', 'patients.id', '=', 'operation_returns.patient_id')
                ->leftJoin('store_statuses', 'store_statuses.id', '=', 'operation_returns.status_id')
                ->join('return_items', 'return_items.operation_return_id', '=', 'operation_returns.id')
                ->join('medications', 'medications.med_id', '=', 'return_items.med_id')
                ->groupBy(
                    'return_items.id','store_statuses.name', 'store_statuses.class',
                    'store_statuses.id', 'operation_returns.reason', 'medications.med_id',
                    'medications.name', 'return_items.quantity', 'return_items.id',
                    'return_items.rx_number', 'operation_returns.date', 'operation_returns.user_id'
                );
                
            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        if ($column['name'] === 'patient_name') {
                            // Handle search for 'patient_name' field differently
                            $query->orWhere(DB::raw('CONCAT(patients.firstname, " ", patients.lastname)'), 'like', "%{$search}%");
                        } 
                        else {
                            // Default search behavior for other columns
                            $query->orWhere("$column[name]", 'like', "%".$search."%");
                        }       
                    }  
                }  
                
            });

            //default field for order
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];

            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $val) {

                $actions = '<div class="d-flex order-actions">';
                if(Auth::user()->can('menu_store.operations.rts.update')) {
                    // data-pid="'.$val->patient_id.'"
                    if(auth()->user()->id == $val->user_id){
                        $icon = 'fa-pencil';
                    }
                    else{
                        $icon = 'fa-eye';
                    }
                        $actions .= '<button type="button" data-id="'.$val->id.'"
                                data-id="'.$val->id.'" data-uid="'.$val->user_id.'"
                                data-date="'.$val->date.'" data-sid="'.$val->status_id.'"
                                data-mid="'.$val->med_id.'" data-quantity="'.$val->quantity.'"
                                data-rxnumber="'.$val->rx_number.'" data-drugname="'.$val->med_name.'"
                                data-patientname="'.$val->patient_name.'" data-statusname="'.$val->status_name.'"
                                data-reason="'.$val->reason.'"
                                onclick="showEditForm(this)" class="btn btn-primary btn-sm me-2" ><i class="fa-solid '.$icon.'"></i></button>';
                    
                }
                if(Auth::user()->can('menu_store.operations.rts.update')) {
                    $actions .= '<button type="button" onclick="showConfirmDeleteForm(' . $val->return_item_id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>';
                }
                $actions .= '</div>';
                $newData[] = [
                    'id' => $val->id,
                    // 'patient_name' => $val->patient_name,
                    'med_name' => $val->med_name,
                    'quantity' => $val->quantity,
                    'rx_number' => $val->rx_number,
                    'date' => ($val->date)?date('Y-m-d', strtotime($val->date)):'',
                    'status' => ($val->status_name)?'<button type="button" class="btn btn-'.$val->status_class.' btn-sm">'.$val->status_name.'</button>':'',
                    // 'actions' =>  '<div class="d-flex order-actions">
                    //     <button type="button" onclick="showConfirmDeleteForm(' . $val->return_item_id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    // </div>',
                    'actions' => $actions,
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }
}
