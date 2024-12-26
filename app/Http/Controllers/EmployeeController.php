<?php

namespace App\Http\Controllers;

use App\Models\PharmacyStaff;
use App\Models\Employee;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();

                if($request->has('employee'))
                {
                    $role = null;

                    $data = $request->employee;
                    $employee = new Employee();
                    foreach($data as $k => $v) {
                        if(!empty($v)) {
                            if($k == 'date_of_birth' || $k == 'start_date' || $k == 'end_date') {
                                $v = date('Y-m-d', strtotime($v));
                            }
                            $employee->$k = $v;
                        }
                    }
                    $employee->initials_random_color = rand(1, 10);
                    if($request->has('is_offshore')) {
                        $employee->is_offshore = $request->is_offshore;
                    }
                    $save = $employee->save();

                    if($save) {
                        if($request->has('pharmacyStaff'))
                        {
                            // if($request->is_offshore == 0) {
                                $data = $request->pharmacyStaff;
                                $pharmacyStaff = new PharmacyStaff();
                                $pharmacyStaff->pharmacy_store_id = $data['pharmacy_store_id'];
                                $pharmacyStaff->employee_id = $employee->id;
                                $save = $pharmacyStaff->save();
                            // } else {
                            //     $save = true;
                            // }

                            if($save) {
                                $role = 'pharmacist';
                            }
                        }
                    } else {
                        throw new \Exception("Not saved");
                    }
                }

                if($request->has('user'))
                {
                    $data = $request->user;
                    $user = new User();
                    $user->name = $data['user_name'];
                    $user->email = $data['user_email'];
                    $user->password = $data['user_password'];
                    $user->type_id = 1;
                    $user->role_id = 7;
                    $save = $user->save();

                    $employee->user_id = $user->id;
                    $employee->save();

                    if($save && !empty($role)) {
                        $user->assignRole($role);
                    }
                }

                if(!$save) {
                    throw new \Exception("Not saved");
                }

                DB::commit();

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

}
