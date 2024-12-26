<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Helper;
use App\Http\Requests\User\StoreUserRequest;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\PharmacyStaff;
use App\Models\PharmacyStore;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSettingController extends Controller
{    
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->middleware('permission:user.index|user.create|user.update|user.delete');
    }

    public function index()
    {
        $user = Auth::user();

        $viewPath = '';
        switch ($user->userType->id) {
            case 1:
                $viewPath = '/systemUsers/users/index';
                break;
            // Add more cases as needed
        }

        $breadCrumb = ['System Users', 'User'];
        return view($viewPath, compact('user', 'breadCrumb'));
    }

    public function delete_user(Request $request)
    {
        if($request->ajax()){
            
            try {
                DB::beginTransaction();
                //code...
                $input = $request->all();
                $user = User::find($input['id']);
                $user->delete();

                $emp = Employee::where('user_id', $input['id'])->first();
                if(isset($emp->id)) {
                    $emp->user_id = null;
                    $emp->save();
                }

                DB::commit();
                return json_encode([
                    'data'=>$user->id,
                    'status'=>'success',
                    'message'=>'Record has been deleted.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in UserController.delete_user.db_transaction.'
                ]);
            }
        }
    }

    public function add_user(StoreUserRequest $request)
    {
        if($request->ajax()){
            try {
                DB::beginTransaction();
                //$data = $request->validated();
                $req = $request->all();
                $user = new User();
                // $user->create($data);
                $user->name = $req['name'];
                $user->email = $req['email'];
                $user->password = $req['password'];
                $user->role_id = $req['role_id'];
                $user->type_id = 1;
                $user->save();

                $role = Role::findOrFail($req['role_id']);
                $user->assignRole($role->name);
                
                $emp = Employee::where('id', $req['employee_id'])->first();
                $emp->user_id = $user->id;
                $emp->save();

                $stores = PharmacyStore::all();
                foreach($stores as $store) {
                    $pname = 'menu_store.'.$store->id;
                    if($role->hasPermissionTo($pname)) {
                        $staff = new PharmacyStaff();
                        $staff->employee_id = $emp->id;
                        $staff->pharmacy_store_id = $store->id;
                        $staff->save();
                    }
                }
                
                DB::commit();

                return json_encode([
                    'data'=> $user,
                    'status'=>'success',
                    'message'=>'Record has been saved.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in UserController.add_user.db_transaction.'
                ]);
            }
        }
    }

    public function update_user(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                $input = $request->all();
                //Storage::disk('local')->append('file.txt', json_encode($input));
                    
                $validation = Validator::make($input, [
                    
                    "name" => 'required|min:2|max:50',
                    'email' => 'required|email|unique:users,email,'.$input['id'].',id',
                    'role_id' => 'required',
                    'password' => [ ($input['password'] != '')?
                            Password::min(8)
                                ->mixedCase()
                                ->numbers()
                                ->symbols():'','confirmed',
                    ],
                ]);
                if ($validation->passes()){
                
                    $user = User::where('id', $input['id'])->first();
                    //$role->update($data);
                    $user->name = $input['name'];
                    $user->email = $input['email'];
                    ($input['password'])?$user->password = $input['password']:'';
                    $user->role_id = $input['role_id'];
                    $user->type_id = 1;
                    $user->save();

                    $role = Role::findOrFail($input['role_id']);

                    $user->roles()->detach();
                    $user->assignRole($role->name);


                    $oldEmp = Employee::where('user_id', $input['id'])->first();
                    if(isset($oldEmp)) {
                        $oldEmp->user_id = 0;
                        $oldEmp->save();
                    }

                    $emp = Employee::where('id', $input['employee_id'])->first();
                    $emp->user_id = $input['id'];
                    $emp->save();

                    DB::commit();
                    return json_encode([
                        'data'=> $user,
                        'status'=>'success',
                        'message'=>'Record has been updated.'
                    ]);
                }
                else{
                    return json_encode(
                        ['status'=>'error',
                        'errors'=> $validation->errors(),
                        'message'=>'Employee saving failed.']);
                }


            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in RoleController.update_role.db_transaction.'
                ]);
            }
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
            //$query = DB::table('view_users');
            //$query = User::select('users.*', 'roles.name AS role')
                // ->join('roles', 'users.role_id', '=', 'roles.id')
                // ->get();
                
            $query = User::with('employee')->select('users.*', 'employees.firstname', 'employees.lastname', 
                'roles.display_name AS role')
                ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id');
            


            // Search //input all searchable fields
            $search = $request->search;
            $columns = $request->columns;
            $query = $query->where(function($query) use ($search, $columns){
                foreach ($columns as $column) {
                    if($column['searchable'] === "true"){
                        $query->orWhere("$column[name]", 'like', "%".$search."%");
                    }  
                }   
            });

            $query = $query->where(function($query) {
                $query->whereHas('employee', function($query) {
                    $query->whereNot('status', 'Terminated');
                    $query->where('is_test', 0);
                });
            });

            

            //default field for order
            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            if($orderByCol == 'avatar') {
                $query = $query->orderBy('employees.firstname', $orderBy);
                $query = $query->orderBy('employees.lastname', $orderBy);
            } else {
                $query = $query->orderBy($orderByCol, $orderBy);
            }
            
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            
            $newData = [];
            foreach ($data as $value) {
                $emp = isset($value->employee->id) ? $value->employee : [];

                $canEdit = Auth::user()->can('user.update') ? true : false;

                $fullname = $emp->firstname.' '.$emp->lastname;
                if($canEdit === true) {
                    $fullname = '<span class="clickable-text" onClick="clickUserEmployeeName('.$value->id.')">'.$fullname.'</span>';
                }

                if(!empty($emp->image)) {
                    $avatar = '
                        <div class="d-flex">
                            <img src="/upload/userprofile/'.$emp->image.'" width="35" height="35" class="rounded-circle" alt="">
                            <div class="flex-grow-1 ms-3 mt-2">
                                <p class="font-weight-bold mb-0">'.$fullname.'</p>
                            </div>
                        </div>
                    ';
                } else {
                    $avatar = '
                        <div class="d-flex">
                            <div class="employee-avatar-'.$emp->initials_random_color.'-initials hr-employee" data-id="'.$emp->id.'">
                            '.strtoupper(substr($emp->firstname, 0, 1)).strtoupper(substr($emp->lastname, 0, 1)).'
                            </div>
                            <p class="font-weight-bold mb-0 ms-3 mt-2">'.$fullname.'</p>
                        </div>
                    ';
                }

                $actions = '<div class="d-flex order-actions">';
                if(Auth::user()->can('user.update')) {
                    $actions .= '<button class="btn btn-primary btn-sm me-2" id="user_edit_btn_'.$value->id.'"
                        data-name="'.$value->name.'" data-email="'.$value->email.'" 
                        data-id="'.$value->id.'" data-roleid="'.$value->role_id.'"
                        data-empid="'.($emp->id ?? null).'" onclick="showEditForm(this);">
                        <i class="fa fa-pencil"></i>
                    </button>';
                }
                if(Auth::user()->can('user.delete')) {
                    $actions .= ' <button class="btn btn-danger btn-sm" 
                        onclick="ShowConfirmDeleteForm(' . $value->id . ')">
                        <i class="fa fa-trash-can"></i>
                    </button>';
                }
                $actions .= '</div>';
                $newData[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'email' => $value->email,
                    'role' => $value->role,
                    'avatar' => $avatar,
                    'actions' => $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }

}


