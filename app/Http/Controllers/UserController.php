<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ApiKey;
use App\Models\Employee;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller

{    
    private const PATH_EMP_AVATAR = 'upload/userprofile';

    public function generatekey()
    {
        $user = auth()->check() ? Auth::user() : redirect()->route('login');

        $apiKey = ApiKey::create([
            'key' => Str::random(40), // Generate a random API key
            'user_name' =>  strtolower(str_replace(' ', '', $user->name)),
            'user_id' => $user->id, // Associate the key with the user
           
        ]);
    
        return response()->json(
            ['api_key' => $apiKey->key,'user_id' => $apiKey->user_id]); 
    }

    public function security_profile()
    {
        $user = Auth::user();

        $profileData = $this->profileData($user->id);

        $breadCrumb = ['Profile', 'Security'];
        return view('/profile/userProfile/security', compact('user', 'breadCrumb', 'profileData'));
    }

    public function update_password(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            //code...
            $input = $request->all();   
            $user = User::where('id', $user->id)->first();
            $helper = new Helper;
            $validation = Validator::make($input, [ 
                'password_confirmation' => 'required',
                'current_password' => 'required|current_password',
                'password' => [ ($input['password'] != '')?
                                Password::min(8)
                                    ->mixedCase()
                                    ->numbers()
                                    ->symbols():'','confirmed','required'],
            ]);
            if ($validation->passes()){
                       
                $user->password = Hash::make($input['password']);
                $user->save();
                
                DB::commit();

                return redirect()->back()->with('success', 'success')->with('success_body', 'Password Updated!');
            }
            else{
                return back()->withErrors($validation);  
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in UserController.updateProfile.'
            ]);
        }
        
    }

    private function profileData($id)
    {
        DB::beginTransaction();
        try {
            //code...
            $profileData = Employee::select('employees.*', 'users.email as work_email')
                ->join('users', 'users.id', '=', 'employees.user_id')
                ->where('user_id', $id)
                ->first();

            DB::commit();
            return $profileData;
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in UserController.profileData.'
            ]);
        }
    }

    public function profile_view()
    {
        $user = Auth::user();
        
        $profileData = $this->profileData($user->id);
       
        $viewPath = '';
        switch ($user->role->id) {
            case 1:
                $viewPath = '/profile/userProfile/index';
                break;
            case 2:
                $viewPath = '/profile/userProfile/index';
                break;
            // Add more cases as needed
            default:
                $viewPath = '/profile/userProfile/index';
            break;
        }

        return view($viewPath, compact('user', 'profileData'));
    }

    public function editProfile_view()
    {
        $user = Auth::user();
        
        $profileData = $this->profileData($user->id);
        
        $viewPath = '';
        switch ($user->role->id) {
            case 1:
                $viewPath = '/profile/editProfile/index';
                break;
            case 2:
                $viewPath = '/profile/editProfile/index';
                break;
            // Add more cases as needed
        }

        return view($viewPath, compact('user', 'profileData'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            //code...
            $input = $request->all();   
            $emp = Employee::where('user_id', $user->id)->first();
            $helper = new Helper;
            $validation = Validator::make($input, [ 
                "firstname" => 'required|min:2|max:50',
                "lastname" => 'required|min:2|max:50',
                "nickname" => 'required|min:1|max:50',
                "date_of_birth" => 'required||before:'.now()->subYears(18)->toDateString(),
                "contact_number"=> 'required|numeric|min:11',
                "address" => 'required|min:2|max:50',
                "email" => ['required', 'email', 'max:255', Rule::unique(Employee::class)->ignore($emp->id)],
            ]);
            if ($validation->passes()){
                
                //$emp->fill($request);
                $emp->firstname = $helper->ProperNamingCase($input['firstname']);
                $emp->lastname = $helper->ProperNamingCase($input['lastname']);
                $emp->nickname = $helper->ProperNamingCase($input['nickname']);
                $emp->date_of_birth = $input['date_of_birth'];
                $emp->contact_number = $input['contact_number'];
                $emp->email = $input['email'];
                $emp->address = $input['address'];
                if ($request->file('image')) {
                    $file = $request->file('image');
                    @unlink(public_path('upload/userprofile/'.$emp->image));
                    $fileName = date('YmdHi').$file->getClientOriginalName();
                    $file->move(public_path('upload/userprofile'), $fileName);

                    $emp->image = $fileName;
                }
                $emp->save();
                
                DB::commit();

                return redirect()->back()->with('status', 'profile-updated');
            }
            else{
                return back()->withErrors($validation);  
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in UserController.updateProfile.'
            ]);
        }
        
    }

    public function update_avatar(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                try {   
                    $data = json_decode($request->data);                 
                    $employee = Employee::findOrFail($data->id);
                    if ($request->file('image')) {
                        $file = $request->file('image');
                        @unlink(public_path(self::PATH_EMP_AVATAR.'/'.$employee->image));
                        $fileName = date('YmdHi').'_'.$file->getClientOriginalName();
                        $file->move(public_path(self::PATH_EMP_AVATAR), $fileName);
                        $employee->image = $fileName;
                    }
                    $save = $employee->save();

                    if(!$save) {
                        throw "Not saved";
                    }

                    DB::commit();

                    return json_encode([
                        'data'=> $employee,
                        'status'=>'success',
                        'message'=>'Record has been updated.'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack(); 
                    return response()->json([
                        'error' => $e->getMessage(),
                        'message' => 'Something went wrong in UserController.update_avatar.db_transaction.'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in UserController.update_avatar.'
                ]);
            }
        }
    }

    public function get_roles(Request $request)
    {
        if($request->ajax()){
            $data = Role::get();

            return json_encode([
                'data'=> $data,
            ]);
        }

    }

    public function get_employees(Request $request)
    {
        if($request->ajax()){
            $data = Employee::where('id','>',19);

            $data->where(function($data){
                $data->orWhere('user_id', 0);
                $data->orWhereNull('user_id');
            });

            $data = $data->get();

            return json_encode([
                'data'=> $data,
            ]);
        }

    }

    public function get_active_employees(Request $request)
    {
        if($request->ajax()){
            $data = Employee::where('user_id', '>', 19)->where("status", "!=", "Terminated")->get();

            return json_encode([
                'data'=> $data,
            ]);
        }

    }


    public function add_user(StoreUserRequest $request)
    {
        if($request->ajax()){
            try{
                DB::beginTransaction();
                try {
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
                    
                    $emp = Employee::where('id', $req['employee_id'])->first();
                    $emp->user_id = $user->id;
                    $emp->save();
                    
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
            }catch(\Exception $e){
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in UserController.add_user.'
                ]);
            }
        }
    }

    public function update_user(Request $request)
    {
        if($request->ajax()){

            try {
                DB::beginTransaction();
                try {
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


                        // $oldEmp = Employee::where('user_id', $input['id'])->first();
                        // $oldEmp->user_id = 0;
                        // $oldEmp->save();

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
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Something went wrong in RoleController.update_role.'
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
                
            $query = User::select('users.*', 'roles.name AS role')
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

            //default field for order
            
            $orderByCol = $request->columns[$request->order[0]['column']]['name'];
            
            $query = $query->orderBy($orderByCol, $orderBy);
            $recordsFiltered = $recordsTotal = $query->count();
            $data = $query->skip($skip)->take($pageLength)->get();

            
            $newData = [];
            foreach ($data as $value) {
                $emp = Employee::where('user_id', $value->id)->first();
                $actions = '<div class="d-flex order-actions">';
                if(Auth::user()->can('user.update')) {
                    $actions .= '<a 
                    data-name="'.$value->name.'" data-email="'.$value->email.'" 
                    data-id="'.$value->id.'" data-roleid="'.$value->role_id.'"
                    data-empid="'.($emp->id ?? null).'" onclick="showEditForm(this);"
                    class="btn-primary" style="background-color:#8833ff"><i class="bx bxs-edit"></i></a>';
                }
                if(Auth::user()->can('user.delete')) {
                    $actions .= '<a onclick="ShowConfirmDeleteForm(' . $value->id . ')" class="btn-danger ms-3" style="background-color:#dc362e"><i class="bx bxs-trash"></i></a>';
                }
                $actions .= '</div>';
                $newData[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'email' => $value->email,
                    'role' => $value->role,
                    'actions' => $actions
                ];
            }   
            
            return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $newData], 200);
        }

    }


    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    } 

}


