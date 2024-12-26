<?php

namespace App\Http\Controllers;

use App\Models\PharmacyOperation;
use App\Models\PharmacySupport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PharmacyOperationController extends Controller
{
    private const PATH_UPLOAD = 'upload/divsion2b/pharmacy/operations';
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $breadCrumb = ['Division 2B', 'Pharmacy Operation'];

        $operations = PharmacyOperation::all();

        return view('/division2b/pharmacyOperations/index', compact('user', 'breadCrumb', 'operations'));
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

            $query = new PharmacyOperation();


            // Search //input all searchable fields
            $search = $request->search;
            $query = $query->where(function($query) use ($search){
                $query->orWhere('code', 'like', "%".$search."%");   
                $query->orWhere('name', 'like', "%".$search."%");   
            });


            $withTrashed = isset($request->withTrashed) ? $request->withTrashed : false;
            $query->withTrashed($withTrashed);   
            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            $newData = [];
            foreach ($data as $value) {
                $newData[] = [
                    'id' => $value->id,
                    'code' => $value->code,
                    'name' => $value->name,
                    'description' => $value->description,
                    'cover_image' => $value->cover_image,
                    'actions' =>  '<div class="d-flex order-actions">
                        <button type="button" class="btn btn-primary btn-sm me-2" onclick="showEditForm('.$value->id.',\'' . addslashes($value->code) . '\',\'' . addslashes($value->name) . '\',\'' . addslashes($value->description) . '\',\'' . $value->cover_image . '\');"><i class="fa-solid fa-pencil"></i></button>
                        <button type="button" onclick="ShowConfirmDeleteOperationForm(' . $value->id . ')" class="btn btn-danger btn-sm me-2" ><i class="fa-solid fa-trash-can"></i></button>
                    </div>'
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }
    }

    public function add_operation(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {
                    $data = json_decode($request->data);
                    $pharmacyOperation = new PharmacyOperation;
                    $pharmacyOperation->code = $data->code;
                    $pharmacyOperation->name = $data->name;
                    $pharmacyOperation->description = $data->description;
                    if ($request->file('cover_image')) {
                        $file = $request->file('cover_image');
                        @unlink(public_path(self::PATH_UPLOAD.'/'.$pharmacyOperation->cover_image));
                        $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
                        $file->move(public_path(self::PATH_UPLOAD), $fileName);
                        $pharmacyOperation->cover_image = '/'.self::PATH_UPLOAD.'/'.$fileName;
                    }
                    
                    $save = $pharmacyOperation->save();

                    if(!$save) {
                        throw "Not saved";
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $pharmacyOperation,
                        'status'=>'success',
                        'message'=>'Record has been saved.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyOperationController.add_operation.db_transaction.'
                    ]);
                }
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyOperationController.add_operation.'
                ]);
            }
        }
    }

    public function update_operation(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                try {   
                    $data = json_decode($request->data);                 
                    $pharmacyOperation = PharmacyOperation::findOrFail($data->id);
                    $pharmacyOperation->code = $data->code;
                    $pharmacyOperation->name = $data->name;
                    $pharmacyOperation->description = $data->description;
                    if ($request->file('cover_image')) {
                        $file = $request->file('cover_image');
                        @unlink(public_path(self::PATH_UPLOAD.'/'.$pharmacyOperation->cover_image));
                        $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
                        $file->move(public_path(self::PATH_UPLOAD), $fileName);
                        $pharmacyOperation->cover_image = '/'.self::PATH_UPLOAD.'/'.$fileName;
                    }
                    $save = $pharmacyOperation->save();

                    if(!$save) {
                        throw "Not saved";
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $pharmacyOperation,
                        'status'=>'success',
                        'message'=>'Record has been updated.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyOperationController.update_operation.db_transaction.'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyOperationController.update_operation.'
                ]);
            }
        }
    }

    public function delete_operation(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                DB::beginTransaction();
                $pharmacyOperation = PharmacyOperation::findOrFail($request->id);
                $save =  $pharmacyOperation->delete();
                
                $count = PharmacySupport::with('pharmacyOperations')->count();
                if($count > 0) {
                    PharmacySupport::where("pharmacy_operation_id",$request->id)->delete();
                }
                

                if(!$save) {
                    DB::rollback();
                    return response()->json([
                        'error' => "error",
                        'message' => 'Something went wrong in PharmacyOperationController.delete_operation.'
                    ]);
                }

                DB::commit();

                return json_encode([
                    'data'=>$pharmacyOperation,
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyOperationController.delete_operation.'
                ]);
            }
        }
    }

}
