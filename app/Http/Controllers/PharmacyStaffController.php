<?php

namespace App\Http\Controllers;

use App\Models\PharmacyStaff;
use App\Models\Employee;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyStaffController extends Controller
{
    /**
     * Instantiate a new PharmacyStoreController instance.
     */
    public function __construct()
    {
        $this->middleware('permission:pharmacy_staff.index|pharmacy_staff.create|pharmacy_staff.update|pharmacy_staff.delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function searchNames(Request $request)
    {
        // $data = PharmacyStaff::with('employee')
        //         ->select("employee.id", "employee.firstname", "employee.lastname");
        $data = PharmacyStaff::join('employees', 'employees.id', '=', 'pharmacy_staff.employee_id')
                ->select("employees.id", "employees.firstname", "employees.lastname", DB::raw("CONCAT(employees.firstname, ' ', employees.lastname) AS name"));
        if($request->has('pharmacy_store_id')) {
            $data = $data->where('pharmacy_store_id', $request->pharmacy_store_id);
        }
        if($request->has('term')) {
            $data = $data->orWhere('employees.firstname', 'like', "%".$request->term."%");
            $data = $data->orWhere('employees.lastname', 'like', "%".$request->term."%");
        }
        if($request->has('limit')) {
            $data = $data->take($request->limit);
        }

        $data = $data->orderBy('employees.lastname','asc')->orderBy('employees.firstname','asc')->get();
        
        if($request->ajax()) {
            return json_encode(['data'=> $data]);
        }
        return $data;
    }

    public function add_staff(Request $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();
                // $data = $request->all();

                $check = PharmacyStaff::where('pharmacy_store_id', $request->pharmacy_store_id)
                    ->where('employee_id', $request->employee_id)
                    ->first();

                $pharmacyStaff = null;
                
                if(!isset($check->id)) {
                    $pharmacyStaff = new PharmacyStaff;
                    $pharmacyStaff->pharmacy_store_id = $request->pharmacy_store_id;
                    $pharmacyStaff->employee_id = $request->employee_id;
                    if($request->has('schedule')) {
                        $pharmacyStaff->schedule = $request->schedule;
                    }
                    
                    $save = $pharmacyStaff->save();

                    if($save) {
                        $emp = Employee::findOrFail($pharmacyStaff->employee_id);
                        if(!empty($emp->user_id)) {
                            // $rname = 'pharmacy-admin.'.$pharmacyStaff->pharmacy_store_id;
                            $user = User::findOrFail($emp->user_id);
                            if (empty($user->role_id)) {
                                $user->assignRole('pharmacist');
                                $user->role_id = 7;
                                $user->save();
                            }
                        }
                    }
    
                    if(!$save) {
                        throw new Exception('Not saved');
                    }
                }

                DB::commit();

                if(empty($pharmacyStaff)) {
                    return response()->json([
                        'status'=>'warning',
                        'error' => [],
                        'message' => 'The staff already exists'
                    ]);
                }
                return json_encode([
                    'data'=> $pharmacyStaff,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyStaffController.add_staff.db_transaction.'
                ]);
            }
        }
    }

    public function update_staff(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                try {                    
                    $pharmacyStaff = PharmacyStaff::findOrFail($request->id);
                    $pharmacyStaff->pharmacy_store_id = $request->pharmacy_store_id;

                    if($request->has('schedule')) {
                        $pharmacyStaff->schedule = $request->schedule;
                    }

                    $save = $pharmacyStaff->save();

                    if(!$save) {
                        throw "Not saved";
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $pharmacyStaff,
                        'status'=>'success',
                        'message'=>'Record has been updated.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in PharmacyStaffController.update_staff.db_transaction.'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyStaffController.update_staff.'
                ]);
            }
        }
    }

    public function delete_staff(Request $request)
    {
        if($request->ajax()){
            
            try {
                
                $pharmacyStaff = PharmacyStaff::findOrFail($request->id);
                $save =  $pharmacyStaff->delete();

                if(!$save) {
                    throw "Not saved";
                }

                return json_encode([
                    'data'=>$pharmacyStaff,
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in PharmacyStaffController.delete_staff.'
                ]);
            }
        }
    }
}
