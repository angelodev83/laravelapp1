<?php

namespace App\Http\Controllers;

use App\Models\PharmacySupport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacySupportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

            $query = PharmacySupport::with('employee', 'operation');

            // Search //input all searchable fields
            $search = $request->search;
            $query = $query->where(function($query) use ($search){
                $query->whereHas('employee', function($query) use ($search) {
                    $query->where('status', '!=', "Terminated");
                });
            });
            $query = $query->where(function($query) use ($search){
                $query->orWhereHas('employee', function($query) use ($search) {
                    $query->where('firstname', 'like', "%".$search."%")
                          ->orWhere('lastname', 'like', "%".$search."%");
                });
            });
            if($request->has('pharmacy_operation_id')) {
                $query = $query->where('pharmacy_operation_id', $request->pharmacy_operation_id);
            }
            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $empName = $value->employee ? ($value->employee->firstname . ' ' . $value->employee->lastname) : '';
                $operationCode = $value->operation ? ($value->operation->code) : '';
                $operationName = $value->operation ? ($value->operation->name) : '';
                $newData[] = [
                    'id' => $value->id,
                    'pharmacy_operation_id' => $value->pharmacy_operation_id,
                    'employee_name' => $empName,
                    'operation_code' => $operationCode,
                    'operation_name' => $operationName,
                    'schedule' => $value->schedule,
                    'actions' =>  '<div class="d-flex order-actions">
                        <button type="button" class="btn btn-primary btn-sm me-2" onclick="showEditSupportForm('.$value->id.','.$value->pharmacy_operation_id.','.$value->employee_id.',\'' . addslashes($empName) . '\',\'' . addslashes($value->schedule) . '\');"><i class="fa-solid fa-pencil"></i></button>
                        <button type="button" onclick="ShowConfirmDeleteSupportForm(' . $value->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function add_support(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {
                    // $data = $request->all();

                    $pharmacySupport = new PharmacySupport;
                    $pharmacySupport->pharmacy_operation_id = $request->pharmacy_operation_id;
                    $pharmacySupport->employee_id = $request->employee_id;
                    if($request->has('schedule')) {
                        $pharmacySupport->schedule = $request->schedule;
                    }
                    
                    $save = $pharmacySupport->save();

                    if(!$save) {
                        throw "Not saved";
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $pharmacySupport,
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacySupportController.add_support.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacySupportController.add_support.'
                ]);
            }
        }
    }

    public function update_support(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                try {                    
                    $pharmacySupport = PharmacySupport::findOrFail($request->id);
                    $pharmacySupport->pharmacy_operation_id = $request->pharmacy_operation_id;

                    if($request->has('schedule')) {
                        $pharmacySupport->schedule = $request->schedule;
                    }

                    $save = $pharmacySupport->save();

                    if(!$save) {
                        throw "Not saved";
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $pharmacySupport,
                        'status'=>'success',
                        'message'=>'Record has been updated.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacySupportController.update_support.db_transaction.'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacySupportController.update_support.'
                ]);
            }
        }
    }

    public function delete_support(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                $pharmacySupport = PharmacySupport::findOrFail($request->id);
                $save =  $pharmacySupport->delete();

                if(!$save) {
                    throw "Not saved";
                }

                return json_encode([
                    'data'=>$pharmacySupport,
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacySupportController.delete_support.'
                ]);
            }
        }
    }
}
